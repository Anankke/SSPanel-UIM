<?php
require_once dirname(dirname(__FILE__))."/vendor/autoload.php";

class Stackable {
    use \Slim\MiddlewareAwareTrait;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) {
        return $res->write('Center');
    }
}
