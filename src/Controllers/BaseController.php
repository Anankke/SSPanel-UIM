<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\{
    Auth,
    View
};
use Smarty;

/**
 * BaseController
 */
class BaseController
{
    /**
     * @var Smarty
     */
    protected $view;

    /**
     * @var User
     */
    protected $user;

    /**
     * Construct page renderer
     */
    public function __construct()
    {
        $this->view = View::getSmarty();
        $this->user = Auth::getUser();
    }

    /**
     * Get smarty
     *
     * @return Smarty
     */
    public function view()
    {
        if (View::$connection) {
            $this->view->assign('queryLog', View::$connection->connection('default')->getQueryLog())->assign('optTime', (microtime(true) - View::$beginTime) * 1000);
        }
        return $this->view;
    }
}
