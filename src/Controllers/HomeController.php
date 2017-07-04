<?php
namespace App\Controllers;

use Jgut\Slim\Controller\Base as Controller;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        return $this->renderer->render($response, 'home/index.phtml', $args);
    }
}