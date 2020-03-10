<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\{
    Auth,
    View
};
use Slim\Http\Response;
use Psr\Http\Message\ResponseInterface;
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
        return $this->view;
    }

    // TODO: remove
    /**
     * Output JSON
     *
     * @param Response      $response
     * @param array|object  $resource
     *
     * @return ResponseInterface
     */
    public function echoJson(Response $response, $resource)
    {
        return $response->withJson($resource);
    }
}
