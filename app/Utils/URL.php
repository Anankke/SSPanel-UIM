<?php
namespace App\Utils;
use App\Models\User;
use App\Models\Node;
use App\Models\Relay;
use App\Services\Config;
use App\Controllers\LinkController;

class URL
{
    /*
    * 1 SSR can
    * 2 SS can
    * 3 Both can
    */
    public static function CanMethodConnect($method) {
        $ss_aead_method_list = Config::getSupportParam('ss_aead_method');
        if(in_array($method, $ss_aead_method_list)) {
            return 2;
        }
        return 3;
    }
    /*
    * 1 SSR can
    * 2 SS can
    * 3 Both can
    */
    public static function CanProtocolConnect($protocol) {
        if($protocol != 'origin') {
            if(strpos($protocol, '_compatible') === FALSE) {
                return 1;
            }else{
                return 3;
            }
        }
        return 3;
    }
    /*
    * 1 SSR can
    * 2 SS can
    * 3 Both can
    * 4 Both can, But ssr need set obfs to plain
    * 5 Both can, But ss need set obfs to plain
    */
    public static function CanObfsConnect($obfs) {
        if($obfs != 'plain') {
            //SS obfs only
            $ss_obfs = Config::getSupportParam('ss_obfs');
            if(in_array($obfs, $ss_obfs)) {
                if(strpos($obfs, '_compatible') === FALSE) {
                    return 2;
                }else{
                    return 4;//SSR need origin plain
                }
            }else{
                //SSR obfs only
                if(strpos($obfs, '_compatible') === FALSE) {
                    return 1;
                }else{
                    return 5;//SS need plain
                }
            }
        }else{
            return 3;
        }
    }

    public static function parse_args($origin) {
        // parse xxx=xxx|xxx=xxx to array(xxx => xxx, xxx => xxx)
        $args_explode = explode('|', $origin);

        $return_array = [];
        foreach ($args_explode as $arg) {
            $split_point = strpos($arg, '=');

            $return_array[substr($arg, 0, $split_point)] = substr($arg, $split_point + 1);
        }

        return $return_array;
    }

    public static function SSCanConnect($user, $mu_port = 0) {
        if($mu_port != 0) {
            $mu_user = User::where('port', '=', $mu_port)->where("is_multi_user", "<>", 0)->first();
            if ($mu_user == null) {
                return;
            }
            return URL::SSCanConnect($mu_user);
        }
        if(URL::CanMethodConnect($user->method) >= 2 && URL::CanProtocolConnect($user->protocol) >= 2 && URL::CanObfsConnect($user->obfs) >= 2) {
            return true;
        }else{
            return false;
        }
    }

    public static function SSRCanConnect($user, $mu_port = 0) {
        if($mu_port != 0) {
            $mu_user = User::where('port', '=', $mu_port)->where("is_multi_user", "<>", 0)->first();
            if ($mu_user == null) {
                return;
            }
            return URL::SSRCanConnect($mu_user);
        }
        if(URL::CanMethodConnect($user->method) != 2 && URL::CanProtocolConnect($user->protocol) != 2 && URL::CanObfsConnect($user->obfs) != 2) {
            return true;
        }else{
            return false;
        }
    }

    public static function getClashInfo($user) {
        $result = [];
        $v2ray_nodes = $nodes=Node::where(
            function ($query) {
                $query->where('sort', 11);
            }
        )->where(
            function ($query) use ($user){
                $query->where("node_group", "=", $user->node_group)
                    ->orWhere("node_group", "=", 0);
            }
        )->where("type", "1")->where("node_class", "<=", $user->class)->orderBy("name")->get();

        foreach ($v2ray_nodes as $v2ray_node) {
            $node_explode = explode(';', $v2ray_node->server);
            $docs = [
                "name" => $v2ray_node->name,
                "type" => "vmess",
                "server" => $node_explode[0],
                "port" => $node_explode[1],
                "uuid" => $user->getUuid(),
                "alterId" => $node_explode[2],
                "cipher" => "auto",
            ];

            if (count($node_explode) >= 4) {
                if ($node_explode[3] == 'ws') {
                    $docs['network'] = 'ws';
                } else if ($node_explode[3] == 'tls') {
                    $docs['tls'] = true;
                }
            }

            if (count($node_explode) >= 5) {
                if ($node_explode[4] == "ws") {
                    $docs['network'] = 'ws';
                }
            }

            $result[] = $docs;
        }

        if (self::SSCanConnect($user, 0)) {
            $shadowsocks_nodes = Node::where(
                function ($query) {
                    $query->where('sort', 0)
                        ->orwhere('sort', 10);
                }
            )->where(
                function ($query) use ($user){
                    $query->where("node_group", "=", $user->node_group)
                        ->orWhere("node_group", "=", 0);
                }
            )->where("type", "1")->where("node_class", "<=", $user->class)->orderBy("name")->get();

            foreach ($shadowsocks_nodes as $node) {
                $result[] = [
                    "name" => $node->name,
                    "type" => "ss",
                    "server" => $node->server,
                    "port" => $user->port,
                    // TODO: method mapper
                    "cipher" => $user->method,
                    "password" => $user->passwd,
                ];
            }
        }

        return $result;
    }

    public static function getSSConnectInfo($user) {
        $new_user = clone $user;
        if(URL::CanObfsConnect($new_user->obfs) == 5) {
            $new_user->obfs = 'plain';
            $new_user->obfs_param = '';
        }
        if(URL::CanProtocolConnect($new_user->protocol) == 3) {
            $new_user->protocol = 'origin';
            $new_user->protocol_param = '';
        }
        $new_user->obfs = str_replace("_compatible", "", $new_user->obfs);
        $new_user->protocol = str_replace("_compatible", "", $new_user->protocol);
        return $new_user;
    }

    public static function getSSRConnectInfo($user) {
        $new_user = clone $user;
        if(URL::CanObfsConnect($new_user->obfs) == 4) {
            $new_user->obfs = 'plain';
            $new_user->obfs_param = '';
        }
        $new_user->obfs = str_replace("_compatible", "", $new_user->obfs);
        $new_user->protocol = str_replace("_compatible", "", $new_user->protocol);
        return $new_user;
    }
    public static function getAllItems($user, $is_mu = 0, $is_ss = 0) {
        $return_array = array();
        if ($user->is_admin) {
            $nodes=Node::where(
                function ($query) {
                    $query->where('sort', 0)
                        ->orwhere('sort', 10);
                }
            )->where("type", "1")->orderBy("name")->get();
        } else {
            $nodes=Node::where(
                function ($query) {
                    $query->where('sort', 0)
                        ->orwhere('sort', 10);
                }
            )->where(
                function ($query) use ($user){
                    $query->where("node_group", "=", $user->node_group)
                        ->orWhere("node_group", "=", 0);
                }
            )->where("type", "1")->where("node_class", "<=", $user->class)->orderBy("name")->get();
        }
        if($is_mu) {
            if ($user->is_admin) {
            	if ($is_mu!=1){
            		$mu_nodes = Node::where('sort', 9)->where('server', '=', $is_mu)->where("type", "1")->get();
            	}else{
                	$mu_nodes = Node::where('sort', 9)->where("type", "1")->get();
            	}
            } else {
                if ($is_mu!=1){
                    $mu_nodes = Node::where('sort', 9)->where('server', '=', $is_mu)->where('node_class', '<=', $user->class)->where("type", "1")->where(
                        function ($query) use ($user) {
                            $query->where("node_group", "=", $user->node_group)
                                ->orWhere("node_group", "=", 0);
                        }
                    )->get();
                }else{
                    $mu_nodes = Node::where('sort', 9)->where('node_class', '<=', $user->class)->where("type", "1")->where(
                        function ($query) use ($user) {
                            $query->where("node_group", "=", $user->node_group)
                                ->orWhere("node_group", "=", 0);
                        }
                    )->get();
                }
            }
        }
        $relay_rules = Relay::where('user_id', $user->id)->orwhere('user_id', 0)->orderBy('id', 'asc')->get();
        if (!Tools::is_protocol_relay($user)) {
            $relay_rules = array();
        }
        foreach ($nodes as $node) {
            if ($node->mu_only != 1 && $is_mu == 0) {
                if ($node->sort == 10) {
                    $relay_rule_id = 0;
                    $relay_rule = Tools::pick_out_relay_rule($node->id, $user->port, $relay_rules);
                    if ($relay_rule != null) {
                        if ($relay_rule->dist_node() != null) {
                            $relay_rule_id = $relay_rule->id;
                        }
                    }
                    $item = URL::getItem($user, $node, 0, $relay_rule_id, $is_ss);
                    if($item != null) {
                        array_push($return_array, $item);
                    }
                }else{
                    $item = URL::getItem($user, $node, 0, 0, $is_ss);
                    if($item != null) {
                        array_push($return_array, $item);
                    }
                }
            }
            if ($node->custom_rss == 1 && $node->mu_only != -1 && $is_mu != 0) {
                foreach ($mu_nodes as $mu_node) {
                    if ($node->sort == 10) {
                        $relay_rule_id = 0;
                        $relay_rule = Tools::pick_out_relay_rule($node->id, $mu_node->server, $relay_rules);
                        if ($relay_rule != null) {
                            if ($relay_rule->dist_node() != null) {
                                $relay_rule_id = $relay_rule->id;
                            }
                        }
                        $item = URL::getItem($user, $node, $mu_node->server, $relay_rule_id, $is_ss);
                        if($item != null) {
                            array_push($return_array, $item);
                        }
                    }else{
                        $item = URL::getItem($user, $node, $mu_node->server, 0, $is_ss);
                        if($item != null) {
                            array_push($return_array, $item);
                        }
                    }
                }
            }
        }
        return $return_array;
    }
    public static function getAllUrl($user, $is_mu, $is_ss = 0, $enter = 0) {
        $return_url = '';
        $return_url .= URL::getUserTraffic($user, $is_mu).($enter == 0 ? ' ' : "\n");
        $return_url .= URL::getUserClassExpiration($user, $is_mu).($enter == 0 ? ' ' : "\n");
        if(strtotime($user->expire_in)<time()){
			return $return_url;
		}
        $items = URL::getAllItems($user, $is_mu, $is_ss);
        foreach($items as $item) {
            $return_url .= URL::getItemUrl($item, $is_ss).($enter == 0 ? ' ' : "\n");
        }
        if(Config::get('mergeSub') and in_array($is_mu, array(0, 1))){
            $is_mu = $is_mu==0?1:0;
            $items = URL::getAllItems($user, $is_mu, $is_ss);
            foreach($items as $item) {
                $return_url .= URL::getItemUrl($item, $is_ss).($enter == 0 ? ' ' : "\n");
            }
        }
        return $return_url;
    }

    public static function getItemUrl($item, $is_ss) {
        $ss_obfs_list = Config::getSupportParam('ss_obfs');
        if(!$is_ss) {
            $ssurl = $item['address'].":".$item['port'].":".$item['protocol'].":".$item['method'].":".$item['obfs'].":".Tools::base64_url_encode($item['passwd'])."/?obfsparam=".Tools::base64_url_encode($item['obfs_param'])."&protoparam=".Tools::base64_url_encode($item['protocol_param'])."&remarks=".Tools::base64_url_encode($item['remark'])."&group=".Tools::base64_url_encode($item['group']);
            return "ssr://".Tools::base64_url_encode($ssurl);
        } else {
            if($is_ss == 2) {
                $personal_info = $item['method'].':'.$item['passwd']."@".$item['address'].":".$item['port'];
                $ssurl = "ss://".Tools::base64_url_encode($personal_info);
                $ssurl .= "#".rawurlencode(Config::get('appName')." - ".$item['remark']);
            }else{
                $personal_info = $item['method'].':'.$item['passwd'];
                $ssurl = "ss://".Tools::base64_url_encode($personal_info)."@".$item['address'].":".$item['port'];
                $plugin = '';
                if(in_array($item['obfs'], $ss_obfs_list)) {
                    if(strpos($item['obfs'], 'http') !== FALSE) {
                        $plugin .= "obfs-local;obfs=http";
                    } else {
                        $plugin .= "obfs-local;obfs=tls";
                    }
                    if($item['obfs_param'] != '') {
                        $plugin .= ";obfs-host=".$item['obfs_param'];
                    }
                    $ssurl .= "?plugin=".rawurlencode($plugin);
                }
                $ssurl .= "#".rawurlencode(Config::get('appName')." - ".$item['remark']);
            }
            return $ssurl;
        }
    }
    public static function getV2Url($user, $node){
        $node_explode = explode(';', $node->server);
        $item = [
            'v'=>'2',
            'host'=>'',
            'path'=>'',
            'tls'=>''
        ];
        $item['ps'] = $node->name;
        $item['add'] = $node_explode[0];
        $item['port'] = $node_explode[1];
        $item['id'] = $user->getUuid();
        $item['aid'] = $node_explode[2];
        $item['net'] = "tcp";
        $item['type'] = "none";
        if (count($node_explode) >= 4) {
            $item['net'] = $node_explode[3];
            if ($item['net'] == 'ws') {
                $item['path'] = '/';
            } else if ($item['net'] == 'tls') {
                $item['tls'] = 'tls';
            }
        }
        if (count($node_explode) >= 5 ) {
            if (in_array($item['net'], array("kcp", "http"))){

                $item['type'] = $node_explode[4];
            } else if ($node_explode[4]=='ws'){
                $item['net'] = 'ws';
            }
        }

        if (count($node_explode) >= 6) {
            $item = array_merge($item, URL::parse_args($node_explode[5]));
        }

        return "vmess://".base64_encode((json_encode($item, JSON_UNESCAPED_UNICODE)));
    }

    public static function getAllVMessUrl($user) {
        $nodes = Node::where('sort', 11)->where(
            function ($query) use ($user){
                $query->where("node_group", "=", $user->node_group)
                    ->orWhere("node_group", "=", 0);
            }
        )->where("type", "1")->where("node_class", "<=", $user->class)->orderBy("name")->get();

        $result = "";

        foreach ($nodes as $node) {
            $result .= (URL::getV2Url($user, $node) . "\n");
        }

        return $result;
    }

	public static function getAllSSDUrl($user){
		if (!URL::SSCanConnect($user)){
			return null;
		}
		$array_all=array();
		$array_all['airport']=Config::get("appName");
		$array_all['port']=$user->port;
		$array_all['encryption']=$user->method;
		$array_all['password']=$user->passwd;
		$array_all['traffic_used']=Tools::flowToGB($user->u+$user->d);
		$array_all['traffic_total']=Tools::flowToGB($user->transfer_enable);
		$array_all['expiry']=$user->class_expire;
		$array_all['url']=Config::get('baseUrl').'/link/'.LinkController::GenerateSSRSubCode($user->id, 0).'?mu=3';
		$plugin_options='';
		if(strpos($user->obfs,'http')!=FALSE){
			$plugin_options='obfs=http';
		}
		if(strpos($user->obfs,'tls')!=FALSE){
			$plugin_options='obfs=tls';
		}
		if($plugin_options!=''){
			$array_all['plugin']='simple-obfs';//目前只支持这个
			$array_all['plugin_options']=$plugin_options;
			if($user->obfs_param!=''){
				$array_all['plugin_options'].=';obfs-host='.$user->obfs_param;
			}
		}

		$nodes_muport=Node::where('type',1)->where('sort', '=', 9)->orderBy('name')->get();
		$array_server=array();
		$nodes = Node::where('type',1)->where('node_class','<=',$user->class)
			->where(function ($func){
				$func->where('sort', '=', 0)
					->orwhere('sort', '=', 10);
			})
			->where(function ($func) use ($user){
				$func->where('node_group', '=', $user->group)
					->orwhere('node_group', '=', 0);
			})->orderBy('name')->get();
		$server_index=1;
		foreach($nodes as $node){
			$server=array();
			$server['id']=$server_index;
			$server['server']=$node->server;
			//判断是否是中转起源节点
			$relay_rule = Relay::where('source_node_id', $node->id)->where(
				function ($query) use ($user) {
					$query->Where('user_id', '=', $user->id)
						->orWhere('user_id', '=', 0);
				}
			)->orderBy('priority','DESC')->orderBy('id')->first();
			if ($relay_rule != null) {
				//是中转起源节点
				$server['remarks']=$node->name.' => '.$relay_rule->dist_node()->name;
				$server['ratio']=$node->traffic_rate+$relay_rule->dist_node()->traffic_rate;
				array_push($array_server,$server);
				$server_index++;
				continue;
			}

			//不是中转起源节点

			$server['ratio']=$node->traffic_rate;
			//包含普通
			if($node->mu_only==0||$node->mu_only==-1){
			$server['remarks']=$node->name;
				array_push($array_server,$server);
				$server_index++;
			}
			//包含单多
			if($node->mu_only==0||$node->mu_only==1){
				$nodes_muport=Node::where('type','1')->where('sort', '=', 9)
					->where(function ($query) use ($user) {
						$query->Where('node_group', '=', $user->group)
							->orWhere('node_group', '=', 0);
					})
					->where('node_class', '<=', $user->class)
					->orderBy('server')->get();
				foreach($nodes_muport as $node_muport){
					$muport_user=User::where('port','=',$node_muport->server)->first();
					if(!URL::SSCanConnect($muport_user)){
						continue;
					}
					$server['id']=$server_index;
					$server['remarks']=$node->name.' - 单多'.$node_muport->server.'端口';
					$server['port']=$node_muport->server;
					$server['encryption']=$muport_user->method;
					$server['password']=$muport_user->passwd;
					$server['plugin']='simple-obfs';//目前只支持这个
					$plugin_options='';
					if(strpos($muport_user->obfs,'http')!=FALSE){
						$plugin_options='obfs=http';
					}
					if(strpos($muport_user->obfs,'tls')!=FALSE){
						$plugin_options='obfs=tls';
					}
					$server['plugin_options']=$plugin_options.';obfs-host='.$user->getMuMd5();
					array_push($array_server,$server);
					$server_index++;
				}
			}
		}

		$array_all['servers']=$array_server;
		$json_all=json_encode($array_all);

		return 'ssd://'.Tools::base64_url_encode($json_all);
	}

    public static function getJsonObfs($item) {
        $ss_obfs_list = Config::getSupportParam('ss_obfs');
        $plugin = "";
        if(in_array($item['obfs'], $ss_obfs_list)) {
            if(strpos($item['obfs'], 'http') !== FALSE) {
                $plugin .= "obfs-local --obfs http";
            } else {
                $plugin .= "obfs-local --obfs tls";
            }
            if($item['obfs_param'] != '') {
                $plugin .= "--obfs-host ".$item['obfs_param'];
            }
        }
        return $plugin;
    }
    public static function getSurgeObfs($item) {
        $ss_obfs_list = Config::getSupportParam('ss_obfs');
        $plugin = "";
        if(in_array($item['obfs'], $ss_obfs_list)) {
            if(strpos($item['obfs'], 'http') !== FALSE) {
                $plugin .= "obfs=http";
            }
			else {
                $plugin .= "obfs=tls";
            }
            if($item['obfs_param'] != '') {
                $plugin .= ",obfs-host=".$item['obfs_param'];
            }
			else {
				$plugin .= ",obfs-host=wns.windows.com";
			}

        }
        return $plugin;
    }
    /*
    * Conn info
    * address
    * port
    * passwd
    * method
    * remark
    * protocol
    * protocol_param
    * obfs
    * obfs_param
    */
    public static function getItem($user, $node, $mu_port = 0, $relay_rule_id = 0, $is_ss = 0) {
        $relay_rule = Relay::where('id', $relay_rule_id)->where(
            function ($query) use ($user) {
                $query->Where("user_id", "=", $user->id)
                    ->orWhere("user_id", "=", 0);
            }
        )->orderBy('priority','DESC')->orderBy('id')->first();
        $node_name = $node->name;
        if ($relay_rule != null) {
            $node_name .= " - ".$relay_rule->dist_node()->name;
        }
        if($mu_port != 0) {
            $mu_user = User::where('port', '=', $mu_port)->where("is_multi_user", "<>", 0)->first();
            if ($mu_user == null) {
                return;
            }
            $mu_user->obfs_param = $user->getMuMd5();
            $mu_user->protocol_param = $user->id.":".$user->passwd;
            $user = $mu_user;
            if(!Config::get('mergeSub')){
                $node_name .= " - ".$mu_port." 单端口";
            }
        }
        if($is_ss) {
            if(!URL::SSCanConnect($user)) {
                return;
            }
            $user = URL::getSSConnectInfo($user);
        }else{
            if(!URL::SSRCanConnect($user)) {
                return;
            }
            $user = URL::getSSRConnectInfo($user);
        }
        $return_array['address'] = $node->server;
        $return_array['port'] = $user->port;
        $return_array['passwd'] = $user->passwd;
        $return_array['method'] = $user->method;
        $return_array['remark'] = $node_name;
        $return_array['protocol'] = $user->protocol;
        $return_array['protocol_param'] = $user->protocol_param;
        $return_array['obfs'] = $user->obfs;
        $return_array['obfs_param'] = $user->obfs_param;
        $return_array['group'] = Config::get('appName');
        if($mu_port != 0 && !Config::get('mergeSub')) {
            $return_array['group'] .= ' - 单端口';
        }
        return $return_array;
    }
    public static function cloneUser($user) {
        $new_user = clone $user;
        return $new_user;
    }

	public static function getUserTraffic($user, $is_mu = 0){
        $group_name = Config::get('appName');
		if(!Config::get('mergeSub') and $is_mu == 1){
			$group_name .= ' - 单端口';
		}
		if(strtotime($user->expire_in)>time()){
			$ssurl = "www.google.com:1:auth_chain_a:chacha20:tls1.2_ticket_auth:YnJlYWt3YWxs/?obfsparam=&protoparam=&remarks=".Tools::base64_url_encode("剩余流量：".number_format(($user->transfer_enable-($user->u+$user->d))/$user->transfer_enable*100,2)."% ".$user->unusedTraffic())."&group=".Tools::base64_url_encode($group_name);
		}else{
			$ssurl = "www.google.com:1:auth_chain_a:chacha20:tls1.2_ticket_auth:YnJlYWt3YWxs/?obfsparam=&protoparam=&remarks=".Tools::base64_url_encode("账户已过期，请续费后使用")."&group=".Tools::base64_url_encode($group_name);
		}
      	return "ssr://".Tools::base64_url_encode($ssurl);
	}

    public static function getUserClassExpiration($user, $is_mu = 0){
        $group_name = Config::get('appName');
		if(!Config::get('mergeSub') and $is_mu == 1){
			$group_name .= ' - 单端口';
		}
		if(strtotime($user->expire_in)>time()){
			$ssurl = "www.google.com:2:auth_chain_a:chacha20:tls1.2_ticket_auth:YnJlYWt3YWxs/?obfsparam=&protoparam=&remarks=".Tools::base64_url_encode("过期时间：".$user->class_expire)."&group=".Tools::base64_url_encode($group_name);
		}else{
			$ssurl = "www.google.com:2:auth_chain_a:chacha20:tls1.2_ticket_auth:YnJlYWt3YWxs/?obfsparam=&protoparam=&remarks=".Tools::base64_url_encode("账户已过期，请续费后使用")."&group=".Tools::base64_url_encode($group_name);
		}
	return "ssr://".Tools::base64_url_encode($ssurl);
  }
}
