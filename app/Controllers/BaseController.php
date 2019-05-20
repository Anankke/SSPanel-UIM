<?php

namespace App\Controllers;

use App\Services\View;

/**
 * BaseController
 */
class BaseController
{
    /**
     * @var \Smarty
     */
    protected $view;

    /**
     * @var \Slim\Views\PhpRenderer
     */
    protected $renderer;

    /**
     * @var \App\Models\User
     *
     * TODO: private -> protected
     */
    private $user;

    /**
     * Construct page renderer
     */
    public function __construct(\Slim\Container $container)
    {
        $this->view = View::getSmarty();

        // TODO
        $this->user = Auth::getUser();
        $this->renderer = $container->get('renderer');

        if ($this->user->isLogin) {
            define('TEMPLATE_PATH', BASE_PATH . '/templates/views/' . $this->user->theme . '/');
        } else {
            define('TEMPLATE_PATH', BASE_PATH . '/templates/views/' . $_ENV['theme'] . '/');
        }
        
        $this->renderer->setTemplatePath(TEMPLATE_PATH);
        $this->renderer->addAttribute('user', $this->user);
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
