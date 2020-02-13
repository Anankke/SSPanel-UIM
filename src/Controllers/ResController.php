<?php


namespace App\Controllers;

use Gregwar\Captcha\CaptchaBuilder;

class ResController
{
    public function captcha($request, $response, $args)
    {
        $id = $args['id'];
        $builder = new CaptchaBuilder();
        $builder->build();
        //$builder->getPhrase();
        $newResponse = $response->withHeader('Content-type', ' image/jpeg');//->getBody()->write($builder->output());
        $newResponse->getBody()->write($builder->output());
        return $newResponse;
    }
}
