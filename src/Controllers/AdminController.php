<?php
namespace App\Controllers;

use Jgut\Slim\Controller\Base as Controller;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AdminController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        return $this->renderer->render($response, 'admin/index.phtml', $args);
    }
}