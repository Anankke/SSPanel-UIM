#---------------------------------------------------#
## 最后更新时间：{date("Y-m-d h:i:s")}
#---------------------------------------------------#

# HTTP 代理端口
port: 7890

# SOCKS5 代理端口
socks-port: 7891

# Linux 和 macOS 的 redir 代理端口
redir-port: 7892

# 允许局域网的连接
allow-lan: true

# 规则模式：Rule（规则） / Global（全局代理）/ Direct（全局直连）
mode: Rule

# 设置日志输出级别 (默认级别：silent，即不输出任何内容，以避免因日志内容过大而导致程序内存溢出）。
# 5 个级别：silent / info / warning / error / debug。级别越高日志输出量越大，越倾向于调试，若需要请自行开启。
log-level: silent

# Clash 的 RESTful API
external-controller: '0.0.0.0:9090'

# RESTful API 的口令
secret: ''

# 您可以将静态网页资源（如 clash-dashboard）放置在一个目录中，clash 将会服务于 `RESTful API/ui`
# 参数应填写配置目录的相对路径或绝对路径。
# external-ui: folder

Proxy:
{foreach $confs as $conf}
  - {json_encode($conf,320)}
{/foreach}

Proxy Group:
  - { name: "Auto", type: fallback, proxies: {json_encode($proxies,320)}, url: "http://www.gstatic.com/generate_204", interval: 300 }
{append var='proxies' value='Auto' index=0}
  - { name: "Proxy", type: select, proxies: {json_encode($proxies,320)} }
  - { name: "Domestic", type: select, proxies: ["DIRECT","Proxy"] }
{$China_media=["Domestic","Proxy"]}
  - { name: "China_media", type: select, proxies: {json_encode($China_media,320)} }
  - { name: "Global_media", type: select, proxies: ["Proxy"]}
  - { name: "Others", type: select, proxies: ["Proxy","Domestic"]}

Rule:
- DOMAIN,gs.apple.com,Proxy
- DOMAIN-SUFFIX,mzstatic.com,Domestic
- DOMAIN-SUFFIX,akadns.net,Domestic
- DOMAIN-SUFFIX,aaplimg.com,Domestic
- DOMAIN-SUFFIX,cdn-apple.com,Domestic

- DOMAIN,itunes.apple.com, Domestic
- DOMAIN,beta.itunes.apple.com, Domestic
- DOMAIN-SUFFIX,apple.com, Domestic
- DOMAIN-SUFFIX,icloud.com,Domestic
- DOMAIN-SUFFIX,icloud-content.com,Domestic
- DOMAIN,e.crashlytics.com,REJECT

- DOMAIN-KEYWORD,bilibili,China_media
- DOMAIN-SUFFIX,acgvideo.com,China_media
- DOMAIN-SUFFIX,hdslb.com,China_media

- DOMAIN-KEYWORD,qiyi,China_media
- DOMAIN-SUFFIX,qy.net,China_media
- IP-CIDR,101.227.0.0/16,China_media
- IP-CIDR,101.224.0.0/13,China_media
- IP-CIDR,119.176.0.0/12,China_media

- DOMAIN-SUFFIX,api.mob.app.letv.com,China_media

- DOMAIN-KEYWORD,nowtv100,China_media
- DOMAIN-KEYWORD,rthklive,China_media
- DOMAIN-SUFFIX,mytvsuper.com,China_media
- DOMAIN-SUFFIX,tvb.com,China_media

- DOMAIN-SUFFIX,music.126.net,China_media
- DOMAIN-SUFFIX,music.163.com,China_media

- DOMAIN-SUFFIX,vv.video.qq.com,China_media

- IP-CIDR,106.11.0.0/16,China_media


- DOMAIN-SUFFIX,edgedatg.com,Global_media
- DOMAIN-SUFFIX,go.com,Global_media

- DOMAIN,linear-abematv.akamaized.net,Global_media
- DOMAIN-SUFFIX,abema.io,Global_media
- DOMAIN-SUFFIX,abema.tv,Global_media
- DOMAIN-SUFFIX,akamaized.net,Global_media
- DOMAIN-SUFFIX,ameba.jp,Global_media
- DOMAIN-SUFFIX,hayabusa.io,Global_media

- DOMAIN-SUFFIX,amazonaws.com,Global_media

- DOMAIN-SUFFIX,bahamut.com.tw,Global_media
- DOMAIN-SUFFIX,gamer.com.tw,Global_media
- DOMAIN-SUFFIX,hinet.net,Global_media

- DOMAIN-KEYWORD,bbcfmt,Global_media
- DOMAIN-KEYWORD,co.uk,Global_media
- DOMAIN-KEYWORD,uk-live,Global_media
- DOMAIN-SUFFIX,bbc.co,Global_media
- DOMAIN-SUFFIX,bbc.co.uk,Global_media
- DOMAIN-SUFFIX,bbc.com,Global_media
- DOMAIN-SUFFIX,bbci.co,Global_media
- DOMAIN-SUFFIX,bbci.co.uk,Global_media

- DOMAIN-SUFFIX,chocotv.com.tw,Global_media

- DOMAIN-KEYWORD,epicgames,Global_media
- DOMAIN-SUFFIX,helpshift.com,Global_media

- DOMAIN-KEYWORD,foxplus,Global_media
- DOMAIN-SUFFIX,config.fox.com,Global_media
- DOMAIN-SUFFIX,emome.net,Global_media
- DOMAIN-SUFFIX,fox.com,Global_media
- DOMAIN-SUFFIX,foxdcg.com,Global_media
- DOMAIN-SUFFIX,foxnow.com,Global_media
- DOMAIN-SUFFIX,foxplus.com,Global_media
- DOMAIN-SUFFIX,foxplay.com,Global_media
- DOMAIN-SUFFIX,ipinfo.io,Global_media
- DOMAIN-SUFFIX,mstage.io,Global_media
- DOMAIN-SUFFIX,now.com,Global_media
- DOMAIN-SUFFIX,theplatform.com,Global_media

- DOMAIN-SUFFIX,hbo.com,Global_media
- DOMAIN-SUFFIX,hbogo.com,Global_media

- DOMAIN-SUFFIX,hbogoasia.hk,Global_media

- DOMAIN-SUFFIX,happyon.jp,Global_media
- DOMAIN-SUFFIX,hulu.com,Global_media
- DOMAIN-SUFFIX,huluim.com,Global_media

- DOMAIN-SUFFIX,imkan.tv,Global_media

- DOMAIN-SUFFIX,joox.com,Global_media

- DOMAIN-SUFFIX,netflix.com,Global_media
- DOMAIN-SUFFIX,netflix.net,Global_media
- DOMAIN-SUFFIX,nflxext.com,Global_media
- DOMAIN-SUFFIX,nflximg.com,Global_media
- DOMAIN-SUFFIX,nflximg.net,Global_media
- DOMAIN-SUFFIX,nflxso.net,Global_media
- DOMAIN-SUFFIX,nflxvideo.net,Global_media

- DOMAIN-KEYWORD,spotify,Global_media
- DOMAIN-SUFFIX,scdn.co,Global_media
- DOMAIN-SUFFIX,spoti.fi,Global_media

- DOMAIN-SUFFIX,viu.tv,Global_media

- DOMAIN-KEYWORD,youtube,Global_media
- DOMAIN-SUFFIX,googlevideo.com,Global_media
- DOMAIN-SUFFIX,gvt2.com,Global_media
- DOMAIN-SUFFIX,youtu.be,Global_media


- DOMAIN-SUFFIX,cn,Domestic

- DOMAIN-SUFFIX,126.com,Domestic
- DOMAIN-SUFFIX,126.net,Domestic
- DOMAIN-SUFFIX,127.net,Domestic
- DOMAIN-SUFFIX,163.com,Domestic
- DOMAIN-SUFFIX,360buyimg.com,Domestic
- DOMAIN-SUFFIX,36kr.com,Domestic
- DOMAIN-SUFFIX,acfun.tv,Domestic
- DOMAIN-SUFFIX,air-matters.com,Domestic
- DOMAIN-SUFFIX,aixifan.com,Domestic
- DOMAIN-SUFFIX,akamaized.net,Domestic
- DOMAIN-KEYWORD,alicdn,Domestic
- DOMAIN-KEYWORD,alipay,Domestic
- DOMAIN-KEYWORD,taobao,Domestic
- DOMAIN-SUFFIX,amap.com,Domestic
- DOMAIN-SUFFIX,autonavi.com,Domestic
- DOMAIN-KEYWORD,baidu,Domestic
- DOMAIN-SUFFIX,bdimg.com,Domestic
- DOMAIN-SUFFIX,bdstatic.com,Domestic
- DOMAIN-SUFFIX,bilibili.com,Domestic
- DOMAIN-SUFFIX,caiyunapp.com,Domestic
- DOMAIN-SUFFIX,clouddn.com,Domestic
- DOMAIN-SUFFIX,cnbeta.com,Domestic
- DOMAIN-SUFFIX,cnbetacdn.com,Domestic
- DOMAIN-SUFFIX,cootekservice.com,Domestic
- DOMAIN-SUFFIX,csdn.net,Domestic
- DOMAIN-SUFFIX,ctrip.com,Domestic
- DOMAIN-SUFFIX,dgtle.com,Domestic
- DOMAIN-SUFFIX,dianping.com,Domestic
- DOMAIN-SUFFIX,douban.com,Domestic
- DOMAIN-SUFFIX,doubanio.com,Domestic
- DOMAIN-SUFFIX,duokan.com,Domestic
- DOMAIN-SUFFIX,easou.com,Domestic
- DOMAIN-SUFFIX,ele.me,Domestic
- DOMAIN-SUFFIX,feng.com,Domestic
- DOMAIN-SUFFIX,fir.im,Domestic
- DOMAIN-SUFFIX,frdic.com,Domestic
- DOMAIN-SUFFIX,g-cores.com,Domestic
- DOMAIN-SUFFIX,godic.net,Domestic
- DOMAIN-SUFFIX,gtimg.com,Domestic
- DOMAIN,cdn.hockeyapp.net,Domestic
- DOMAIN-SUFFIX,hongxiu.com,Domestic
- DOMAIN-SUFFIX,hxcdn.net,Domestic
- DOMAIN-SUFFIX,iciba.com,Domestic
- DOMAIN-SUFFIX,ifeng.com,Domestic
- DOMAIN-SUFFIX,ifengimg.com,Domestic
- DOMAIN-SUFFIX,ipip.net,Domestic
- DOMAIN-SUFFIX,iqiyi.com,Domestic
- DOMAIN-SUFFIX,jd.com,Domestic
- DOMAIN-SUFFIX,jianshu.com,Domestic
- DOMAIN-SUFFIX,knewone.com,Domestic
- DOMAIN-SUFFIX,le.com,Domestic
- DOMAIN-SUFFIX,lecloud.com,Domestic
- DOMAIN-SUFFIX,lemicp.com,Domestic
- DOMAIN-SUFFIX,luoo.net,Domestic
- DOMAIN-SUFFIX,meituan.com,Domestic
- DOMAIN-SUFFIX,meituan.net,Domestic
- DOMAIN-SUFFIX,mi.com,Domestic
- DOMAIN-SUFFIX,miaopai.com,Domestic
- DOMAIN-SUFFIX,microsoft.com,Domestic
- DOMAIN-SUFFIX,microsoftonline.com,Domestic
- DOMAIN-SUFFIX,miui.com,Domestic
- DOMAIN-SUFFIX,miwifi.com,Domestic
- DOMAIN-SUFFIX,mob.com,Domestic
- DOMAIN-SUFFIX,netease.com,Domestic
- DOMAIN-KEYWORD,officecdn,Domestic
- DOMAIN-SUFFIX,oschina.net,Domestic
- DOMAIN-SUFFIX,ppsimg.com,Domestic
- DOMAIN-SUFFIX,pstatp.com,Domestic
- DOMAIN-SUFFIX,qcloud.com,Domestic
- DOMAIN-SUFFIX,qdaily.com,Domestic
- DOMAIN-SUFFIX,qdmm.com,Domestic
- DOMAIN-SUFFIX,qhimg.com,Domestic
- DOMAIN-SUFFIX,qidian.com,Domestic
- DOMAIN-SUFFIX,qihucdn.com,Domestic
- DOMAIN-SUFFIX,qiniu.com,Domestic
- DOMAIN-SUFFIX,qiniucdn.com,Domestic
- DOMAIN-SUFFIX,qiyipic.com,Domestic
- DOMAIN-SUFFIX,qq.com,Domestic
- DOMAIN-SUFFIX,qqurl.com,Domestic
- DOMAIN-SUFFIX,rarbg.to,Domestic
- DOMAIN-SUFFIX,rr.tv,Domestic
- DOMAIN-SUFFIX,ruguoapp.com,Domestic
- DOMAIN-SUFFIX,segmentfault.com,Domestic
- DOMAIN-SUFFIX,sinaapp.com,Domestic
- DOMAIN-SUFFIX,sogou.com,Domestic
- DOMAIN-SUFFIX,sogoucdn.com,Domestic
- DOMAIN-SUFFIX,sohu.com,Domestic
- DOMAIN-SUFFIX,soku.com,Domestic
- DOMAIN-SUFFIX,speedtest.net,Domestic
- DOMAIN-SUFFIX,sspai.com,Domestic
- DOMAIN-SUFFIX,suning.com,Domestic
- DOMAIN-SUFFIX,taobao.com,Domestic
- DOMAIN-SUFFIX,tenpay.com,Domestic
- DOMAIN-SUFFIX,tmall.com,Domestic
- DOMAIN-SUFFIX,tudou.com,Domestic
- DOMAIN-SUFFIX,umetrip.com,Domestic
- DOMAIN-SUFFIX,upaiyun.com,Domestic
- DOMAIN-SUFFIX,upyun.com,Domestic
- DOMAIN-SUFFIX,v2ex.com,Domestic
- DOMAIN-SUFFIX,veryzhun.com,Domestic
- DOMAIN-SUFFIX,weather.com,Domestic
- DOMAIN-SUFFIX,weibo.com,Domestic
- DOMAIN-SUFFIX,xiami.com,Domestic
- DOMAIN-SUFFIX,xiami.net,Domestic
- DOMAIN-SUFFIX,xiaomicp.com,Domestic
- DOMAIN-SUFFIX,ximalaya.com,Domestic
- DOMAIN-SUFFIX,xmcdn.com,Domestic
- DOMAIN-SUFFIX,xunlei.com,Domestic
- DOMAIN-SUFFIX,yhd.com,Domestic
- DOMAIN-SUFFIX,yihaodianimg.com,Domestic
- DOMAIN-SUFFIX,yinxiang.com,Domestic
- DOMAIN-SUFFIX,ykimg.com,Domestic
- DOMAIN-SUFFIX,youdao.com,Domestic
- DOMAIN-SUFFIX,youku.com,Domestic
- DOMAIN-SUFFIX,zealer.com,Domestic
- DOMAIN-SUFFIX,zhihu.com,Domestic
- DOMAIN-SUFFIX,zhimg.com,Domestic

- DOMAIN-KEYWORD,amazon,Proxy
- DOMAIN-KEYWORD,google,Proxy
- DOMAIN-KEYWORD,gmail,Proxy
- DOMAIN-KEYWORD,youtube,Proxy
- DOMAIN-KEYWORD,facebook,Proxy
- DOMAIN-SUFFIX,fb.me,Proxy
- DOMAIN-SUFFIX,fbcdn.net,Proxy
- DOMAIN-KEYWORD,twitter,Proxy
- DOMAIN-KEYWORD,instagram,Proxy
- DOMAIN-KEYWORD,dropbox,Proxy
- DOMAIN-SUFFIX,twimg.com,Proxy
- DOMAIN-KEYWORD,blogspot,Proxy
- DOMAIN-SUFFIX,youtu.be,Proxy
- DOMAIN-KEYWORD,whatsapp,Proxy

- DOMAIN-SUFFIX,9to5mac.com,Proxy
- DOMAIN-SUFFIX,abpchina.org,Proxy
- DOMAIN-SUFFIX,adblockplus.org,Proxy
- DOMAIN-SUFFIX,adobe.com,Proxy
- DOMAIN-SUFFIX,alfredapp.com,Proxy
- DOMAIN-SUFFIX,amplitude.com,Proxy
- DOMAIN-SUFFIX,ampproject.org,Proxy
- DOMAIN-SUFFIX,android.com,Proxy
- DOMAIN-SUFFIX,angularjs.org,Proxy
- DOMAIN-SUFFIX,aolcdn.com,Proxy
- DOMAIN-SUFFIX,apkpure.com,Proxy
- DOMAIN-SUFFIX,appledaily.com,Proxy
- DOMAIN-SUFFIX,appshopper.com,Proxy
- DOMAIN-SUFFIX,appspot.com,Proxy
- DOMAIN-SUFFIX,arcgis.com,Proxy
- DOMAIN-SUFFIX,archive.org,Proxy
- DOMAIN-SUFFIX,armorgames.com,Proxy
- DOMAIN-SUFFIX,aspnetcdn.com,Proxy
- DOMAIN-SUFFIX,att.com,Proxy
- DOMAIN-SUFFIX,awsstatic.com,Proxy
- DOMAIN-SUFFIX,azureedge.net,Proxy
- DOMAIN-SUFFIX,azurewebsites.net,Proxy
- DOMAIN-SUFFIX,bing.com,Proxy
- DOMAIN-SUFFIX,bintray.com,Proxy
- DOMAIN-SUFFIX,bit.com,Proxy
- DOMAIN-SUFFIX,bit.ly,Proxy
- DOMAIN-SUFFIX,bitbucket.org,Proxy
- DOMAIN-SUFFIX,bjango.com,Proxy
- DOMAIN-SUFFIX,bkrtx.com,Proxy
- DOMAIN-SUFFIX,blog.com,Proxy
- DOMAIN-SUFFIX,blogcdn.com,Proxy
- DOMAIN-SUFFIX,blogger.com,Proxy
- DOMAIN-SUFFIX,blogsmithmedia.com,Proxy
- DOMAIN-SUFFIX,blogspot.com,Proxy
- DOMAIN-SUFFIX,blogspot.hk,Proxy
- DOMAIN-SUFFIX,bloomberg.com,Proxy
- DOMAIN-SUFFIX,box.com,Proxy
- DOMAIN-SUFFIX,box.net,Proxy
- DOMAIN-SUFFIX,cachefly.net,Proxy
- DOMAIN-SUFFIX,chromium.org,Proxy
- DOMAIN-SUFFIX,cl.ly,Proxy
- DOMAIN-SUFFIX,cloudflare.com,Proxy
- DOMAIN-SUFFIX,cloudfront.net,Proxy
- DOMAIN-SUFFIX,cloudmagic.com,Proxy
- DOMAIN-SUFFIX,cmail19.com,Proxy
- DOMAIN-SUFFIX,cnet.com,Proxy
- DOMAIN-SUFFIX,cocoapods.org,Proxy
- DOMAIN-SUFFIX,comodoca.com,Proxy
- DOMAIN-SUFFIX,content.office.net,Proxy
- DOMAIN-SUFFIX,crashlytics.com,Proxy
- DOMAIN-SUFFIX,culturedcode.com,Proxy
- DOMAIN-SUFFIX,d.pr,Proxy
- DOMAIN-SUFFIX,danilo.to,Proxy
- DOMAIN-SUFFIX,dayone.me,Proxy
- DOMAIN-SUFFIX,db.tt,Proxy
- DOMAIN-SUFFIX,deskconnect.com,Proxy
- DOMAIN-SUFFIX,digicert.com,Proxy
- DOMAIN-SUFFIX,disq.us,Proxy
- DOMAIN-SUFFIX,disqus.com,Proxy
- DOMAIN-SUFFIX,disquscdn.com,Proxy
- DOMAIN-SUFFIX,dlercloud.com,Proxy
- DOMAIN-SUFFIX,dnsimple.com,Proxy
- DOMAIN-SUFFIX,docker.com,Proxy
- DOMAIN-SUFFIX,dribbble.com,Proxy
- DOMAIN-SUFFIX,droplr.com,Proxy
- DOMAIN-SUFFIX,duckduckgo.com,Proxy
- DOMAIN-SUFFIX,dueapp.com,Proxy
- DOMAIN-SUFFIX,dytt8.net,Proxy
- DOMAIN-SUFFIX,edgecastcdn.net,Proxy
- DOMAIN-SUFFIX,edgekey.net,Proxy
- DOMAIN-SUFFIX,edgesuite.net,Proxy
- DOMAIN-SUFFIX,engadget.com,Proxy
- DOMAIN-SUFFIX,entrust.net,Proxy
- DOMAIN-SUFFIX,eurekavpt.com,Proxy
- DOMAIN-SUFFIX,evernote.com,Proxy
- DOMAIN-SUFFIX,fabric.io,Proxy
- DOMAIN-SUFFIX,fast.com,Proxy
- DOMAIN-SUFFIX,fastly.net,Proxy
- DOMAIN-SUFFIX,fc2.com,Proxy
- DOMAIN-SUFFIX,feedburner.com,Proxy
- DOMAIN-SUFFIX,feedly.com,Proxy
- DOMAIN-SUFFIX,feedsportal.com,Proxy
- DOMAIN-SUFFIX,fiftythree.com,Proxy
- DOMAIN-SUFFIX,firebaseio.com,Proxy
- DOMAIN-SUFFIX,flexibits.com,Proxy
- DOMAIN-SUFFIX,flickr.com,Proxy
- DOMAIN-SUFFIX,flipboard.com,Proxy
- DOMAIN-SUFFIX,g.co,Proxy
- DOMAIN-SUFFIX,gabia.net,Proxy
- DOMAIN-SUFFIX,geni.us,Proxy
- DOMAIN-SUFFIX,gfx.ms,Proxy
- DOMAIN-SUFFIX,ggpht.com,Proxy
- DOMAIN-SUFFIX,ghostnoteapp.com,Proxy
- DOMAIN-SUFFIX,git.io,Proxy
- DOMAIN-KEYWORD,github,Proxy
- DOMAIN-SUFFIX,globalsign.com,Proxy
- DOMAIN-SUFFIX,gmodules.com,Proxy
- DOMAIN-SUFFIX,godaddy.com,Proxy
- DOMAIN-SUFFIX,golang.org,Proxy
- DOMAIN-SUFFIX,gongm.in,Proxy
- DOMAIN-SUFFIX,goo.gl,Proxy
- DOMAIN-SUFFIX,goodreaders.com,Proxy
- DOMAIN-SUFFIX,goodreads.com,Proxy
- DOMAIN-SUFFIX,gravatar.com,Proxy
- DOMAIN-SUFFIX,gstatic.com,Proxy
- DOMAIN-SUFFIX,gvt0.com,Proxy
- DOMAIN-SUFFIX,hockeyapp.net,Proxy
- DOMAIN-SUFFIX,hotmail.com,Proxy
- DOMAIN-SUFFIX,icons8.com,Proxy
- DOMAIN-SUFFIX,ift.tt,Proxy
- DOMAIN-SUFFIX,ifttt.com,Proxy
- DOMAIN-SUFFIX,iherb.com,Proxy
- DOMAIN-SUFFIX,imageshack.us,Proxy
- DOMAIN-SUFFIX,img.ly,Proxy
- DOMAIN-SUFFIX,imgur.com,Proxy
- DOMAIN-SUFFIX,imore.com,Proxy
- DOMAIN-SUFFIX,instapaper.com,Proxy
- DOMAIN-SUFFIX,ipn.li,Proxy
- DOMAIN-SUFFIX,is.gd,Proxy
- DOMAIN-SUFFIX,issuu.com,Proxy
- DOMAIN-SUFFIX,itgonglun.com,Proxy
- DOMAIN-SUFFIX,itun.es,Proxy
- DOMAIN-SUFFIX,ixquick.com,Proxy
- DOMAIN-SUFFIX,j.mp,Proxy
- DOMAIN-SUFFIX,js.revsci.net,Proxy
- DOMAIN-SUFFIX,jshint.com,Proxy
- DOMAIN-SUFFIX,jtvnw.net,Proxy
- DOMAIN-SUFFIX,justgetflux.com,Proxy
- DOMAIN-SUFFIX,kat.cr,Proxy
- DOMAIN-SUFFIX,klip.me,Proxy
- DOMAIN-SUFFIX,libsyn.com,Proxy
- DOMAIN-SUFFIX,licdn.com,Proxy
- DOMAIN-SUFFIX,linkedin.com,Proxy
- DOMAIN-SUFFIX,linode.com,Proxy
- DOMAIN-SUFFIX,lithium.com,Proxy
- DOMAIN-SUFFIX,littlehj.com,Proxy
- DOMAIN-SUFFIX,live.com,Proxy
- DOMAIN-SUFFIX,live.net,Proxy
- DOMAIN-SUFFIX,livefilestore.com,Proxy
- DOMAIN-SUFFIX,llnwd.net,Proxy
- DOMAIN-SUFFIX,macid.co,Proxy
- DOMAIN-SUFFIX,macromedia.com,Proxy
- DOMAIN-SUFFIX,macrumors.com,Proxy
- DOMAIN-SUFFIX,mashable.com,Proxy
- DOMAIN-SUFFIX,mathjax.org,Proxy
- DOMAIN-SUFFIX,medium.com,Proxy
- DOMAIN-SUFFIX,mega.co.nz,Proxy
- DOMAIN-SUFFIX,mega.nz,Proxy
- DOMAIN-SUFFIX,megaupload.com,Proxy
- DOMAIN-SUFFIX,microsofttranslator.com,Proxy
- DOMAIN-SUFFIX,mindnode.com,Proxy
- DOMAIN-SUFFIX,mobile01.com,Proxy
- DOMAIN-SUFFIX,modmyi.com,Proxy
- DOMAIN-SUFFIX,msedge.net,Proxy
- DOMAIN-SUFFIX,myfontastic.com,Proxy
- DOMAIN-SUFFIX,name.com,Proxy
- DOMAIN-SUFFIX,nextmedia.com,Proxy
- DOMAIN-SUFFIX,nsstatic.net,Proxy
- DOMAIN-SUFFIX,nssurge.com,Proxy
- DOMAIN-SUFFIX,nyt.com,Proxy
- DOMAIN-SUFFIX,nytimes.com,Proxy
- DOMAIN-SUFFIX,office365.com,Proxy
- DOMAIN-SUFFIX,omnigroup.com,Proxy
- DOMAIN-SUFFIX,onedrive.com,Proxy
- DOMAIN-SUFFIX,onenote.com,Proxy
- DOMAIN-SUFFIX,ooyala.com,Proxy
- DOMAIN-SUFFIX,openvpn.net,Proxy
- DOMAIN-SUFFIX,openwrt.org,Proxy
- DOMAIN-SUFFIX,orkut.com,Proxy
- DOMAIN-SUFFIX,osxdaily.com,Proxy
- DOMAIN-SUFFIX,outlook.com,Proxy
- DOMAIN-SUFFIX,ow.ly,Proxy
- DOMAIN-SUFFIX,paddleapi.com,Proxy
- DOMAIN-SUFFIX,parallels.com,Proxy
- DOMAIN-SUFFIX,parse.com,Proxy
- DOMAIN-SUFFIX,pdfexpert.com,Proxy
- DOMAIN-SUFFIX,periscope.tv,Proxy
- DOMAIN-SUFFIX,pinboard.in,Proxy
- DOMAIN-SUFFIX,pinterest.com,Proxy
- DOMAIN-SUFFIX,pixelmator.com,Proxy
- DOMAIN-SUFFIX,pixiv.net,Proxy
- DOMAIN-SUFFIX,playpcesor.com,Proxy
- DOMAIN-SUFFIX,playstation.com,Proxy
- DOMAIN-SUFFIX,playstation.com.hk,Proxy
- DOMAIN-SUFFIX,playstation.net,Proxy
- DOMAIN-SUFFIX,playstationnetwork.com,Proxy
- DOMAIN-SUFFIX,pushwoosh.com,Proxy
- DOMAIN-SUFFIX,rime.im,Proxy
- DOMAIN-SUFFIX,servebom.com,Proxy
- DOMAIN-SUFFIX,sfx.ms,Proxy
- DOMAIN-SUFFIX,shadowsocks.org,Proxy
- DOMAIN-SUFFIX,sharethis.com,Proxy
- DOMAIN-SUFFIX,shazam.com,Proxy
- DOMAIN-SUFFIX,skype.com,Proxy
- DOMAIN-SUFFIX,smartdnsProxy.com,Proxy
- DOMAIN-SUFFIX,smartmailcloud.com,Proxy
- DOMAIN-SUFFIX,sndcdn.com,Proxy
- DOMAIN-SUFFIX,sony.com,Proxy
- DOMAIN-SUFFIX,soundcloud.com,Proxy
- DOMAIN-SUFFIX,sourceforge.net,Proxy
- DOMAIN-SUFFIX,spotify.com,Proxy
- DOMAIN-SUFFIX,squarespace.com,Proxy
- DOMAIN-SUFFIX,sstatic.net,Proxy
- DOMAIN-SUFFIX,st.luluku.pw,Proxy
- DOMAIN-SUFFIX,stackoverflow.com,Proxy
- DOMAIN-SUFFIX,startpage.com,Proxy
- DOMAIN-SUFFIX,staticflickr.com,Proxy
- DOMAIN-SUFFIX,steamcommunity.com,Proxy
- DOMAIN-SUFFIX,symauth.com,Proxy
- DOMAIN-SUFFIX,symcb.com,Proxy
- DOMAIN-SUFFIX,symcd.com,Proxy
- DOMAIN-SUFFIX,tapbots.com,Proxy
- DOMAIN-SUFFIX,tapbots.net,Proxy
- DOMAIN-SUFFIX,tdesktop.com,Proxy
- DOMAIN-SUFFIX,techcrunch.com,Proxy
- DOMAIN-SUFFIX,techsmith.com,Proxy
- DOMAIN-SUFFIX,thepiratebay.org,Proxy
- DOMAIN-SUFFIX,theverge.com,Proxy
- DOMAIN-SUFFIX,time.com,Proxy
- DOMAIN-SUFFIX,timeinc.net,Proxy
- DOMAIN-SUFFIX,tiny.cc,Proxy
- DOMAIN-SUFFIX,tinypic.com,Proxy
- DOMAIN-SUFFIX,tmblr.co,Proxy
- DOMAIN-SUFFIX,todoist.com,Proxy
- DOMAIN-SUFFIX,trello.com,Proxy
- DOMAIN-SUFFIX,trustasiassl.com,Proxy
- DOMAIN-SUFFIX,tumblr.co,Proxy
- DOMAIN-SUFFIX,tumblr.com,Proxy
- DOMAIN-SUFFIX,tweetdeck.com,Proxy
- DOMAIN-SUFFIX,tweetmarker.net,Proxy
- DOMAIN-SUFFIX,twitch.tv,Proxy
- DOMAIN-SUFFIX,txmblr.com,Proxy
- DOMAIN-SUFFIX,typekit.net,Proxy
- DOMAIN-SUFFIX,ubertags.com,Proxy
- DOMAIN-SUFFIX,ublock.org,Proxy
- DOMAIN-SUFFIX,ubnt.com,Proxy
- DOMAIN-SUFFIX,ulyssesapp.com,Proxy
- DOMAIN-SUFFIX,urchin.com,Proxy
- DOMAIN-SUFFIX,usertrust.com,Proxy
- DOMAIN-SUFFIX,v.gd,Proxy
- DOMAIN-SUFFIX,vimeo.com,Proxy
- DOMAIN-SUFFIX,vimeocdn.com,Proxy
- DOMAIN-SUFFIX,vine.co,Proxy
- DOMAIN-SUFFIX,vivaldi.com,Proxy
- DOMAIN-SUFFIX,vox-cdn.com,Proxy
- DOMAIN-SUFFIX,vsco.co,Proxy
- DOMAIN-SUFFIX,vultr.com,Proxy
- DOMAIN-SUFFIX,w.org,Proxy
- DOMAIN-SUFFIX,w3schools.com,Proxy
- DOMAIN-SUFFIX,webtype.com,Proxy
- DOMAIN-SUFFIX,wikiwand.com,Proxy
- DOMAIN-SUFFIX,wikileaks.org,Proxy
- DOMAIN-SUFFIX,wikimedia.org,Proxy
- DOMAIN-SUFFIX,wikipedia.com,Proxy
- DOMAIN-SUFFIX,wikipedia.org,Proxy
- DOMAIN-SUFFIX,windows.com,Proxy
- DOMAIN-SUFFIX,windows.net,Proxy
- DOMAIN-SUFFIX,wire.com,Proxy
- DOMAIN-SUFFIX,wordpress.com,Proxy
- DOMAIN-SUFFIX,workflowy.com,Proxy
- DOMAIN-SUFFIX,wp.com,Proxy
- DOMAIN-SUFFIX,wsj.com,Proxy
- DOMAIN-SUFFIX,wsj.net,Proxy
- DOMAIN-SUFFIX,xda-developers.com,Proxy
- DOMAIN-SUFFIX,xeeno.com,Proxy
- DOMAIN-SUFFIX,xiti.com,Proxy
- DOMAIN-SUFFIX,yahoo.com,Proxy
- DOMAIN-SUFFIX,yimg.com,Proxy
- DOMAIN-SUFFIX,ying.com,Proxy
- DOMAIN-SUFFIX,yoyo.org,Proxy
- DOMAIN-SUFFIX,ytimg.com,Proxy

- DOMAIN-SUFFIX,telegra.ph,Proxy
- DOMAIN-SUFFIX,telegram.org,Proxy
- IP-CIDR,91.108.56.0/22,Proxy
- IP-CIDR,91.108.4.0/22,Proxy
- IP-CIDR,91.108.8.0/22,Proxy
- IP-CIDR,109.239.140.0/24,Proxy
- IP-CIDR,149.154.160.0/20,Proxy
- IP-CIDR,149.154.164.0/22,Proxy

- DOMAIN-SUFFIX,local,DIRECT
- IP-CIDR,127.0.0.0/8,DIRECT
- IP-CIDR,172.16.0.0/12,DIRECT
- IP-CIDR,192.168.0.0/16,DIRECT
- IP-CIDR,10.0.0.0/8,DIRECT
- IP-CIDR,17.0.0.0/8,DIRECT
- IP-CIDR,100.64.0.0/10,DIRECT

- GEOIP,CN,Domestic
- MATCH,Others
