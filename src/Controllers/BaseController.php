<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Services\Auth;
use App\Services\View;
use Smarty;
use Twig\Environment;

abstract class BaseController
{
    /**
     * @var Smarty
     */
    protected Smarty $view;

    /**
     * @var Environment
     */
    protected Environment $twig;

    /**
     * @var User
     */
    protected User $user;

    /**
     * Construct page renderer
     */
    public function __construct()
    {
        $this->user = Auth::getUser();
    }

    /**
     * Get smarty
     */
    public function view(): Smarty
    {
        $this->view = View::getSmarty();

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

    /**
     * Get twig
     */
    public function twig(): Environment
    {
        $this->twig = View::getTwig();

        if (View::$connection) {
            $this->twig->addGlobal(
                'queryLog',
                View::$connection
                    ->connection('default')
                    ->getQueryLog()
            );
            $this->twig->addGlobal('optTime', (microtime(true) - View::$beginTime) * 1000);
        }

        return $this->twig;
    }
}
