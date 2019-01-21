<?php

namespace App\Controllers;

use App\Services\Auth;
use App\Services\View;

/**
 * BaseController
 */

class BaseController
{
    public $view;

    public $smarty;
    
    public function construct__()
    {
    }

    public function smarty()
    {
        $this->smarty = View::getSmarty();
        return $this->smarty;
    }

    public function view()
    {
        return $this->smarty();
    }

    /**
     * @param $response
     * @param $res
     * @return mixed
     */
    public function echoJson($response, $res)
    {
        return $response->getBody()->write(json_encode($res));
    }
}
