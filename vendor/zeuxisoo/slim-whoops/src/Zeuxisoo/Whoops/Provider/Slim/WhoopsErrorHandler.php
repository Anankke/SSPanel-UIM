<?php
namespace Zeuxisoo\Whoops\Provider\Slim;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Whoops\Run as WhoopsRun;

class WhoopsErrorHandler {

    private $whoops;

    public function __construct(WhoopsRun $whoops) {
        $this->whoops = $whoops;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $exception) {
        $handler = WhoopsRun::EXCEPTION_HANDLER;

        ob_start();

        $this->whoops->$handler($exception);

        $content = ob_get_clean();
        $code    = $exception instanceof HttpException ? $exception->getStatusCode() : 500;

        return $response
                ->withStatus($code)
                ->withHeader('Content-type', 'text/html')
                ->write($content);
    }

    private function renderException(Exception $exception) {

    }

}
