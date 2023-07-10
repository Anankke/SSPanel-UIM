<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Services\Auth;
use App\Services\View;
use Smarty;

/**
 * BaseController
 */
abstract class BaseController
{
    /**
     * @var Smarty
     */
    protected Smarty $view;

    /**
     * @var User
     */
    protected User $user;

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
     */
    public function view(): Smarty
    {
        if (View::$connection) {
            $this->view->assign(
                'queryLog',
                View::$connection
                    ->connection('default')
                    ->getQueryLog()
            )->assign('optTime', (microtime(true) - View::$beginTime) * 1000);
        }
        return $this->view;
    }
}
