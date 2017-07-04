<?php
namespace App\Controllers\Api;

use Jgut\Slim\Controller\Base as Controller;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Minishlink\WebPush\WebPush;

class PushMessagesController extends Controller
{
    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $data = $request->getParsedBody();
            $this->triggerPushMessages($data);
            return $response
              ->withHeader('Content-Type', 'application/json')
              ->withJson($data);
        } catch (Exception $e) {
            return $response->withStatus(500);
        }
    }

    private function triggerPushMessages($message)
    {
        foreach ($this->pushSubscriptionStore->findAll() as $subscription) {
            $this->triggerPushMessage($message, $subscription, false);
        }
        $result = $this->messagePusher->flush();
        var_dump($result);
    }

    private function triggerPushMessage($message, $subscription, $flush = true)
    {
        $result = $this->messagePusher->sendNotification(
            $subscription->endpoint,
            json_encode($message),
            $subscription->user_public_key,
            $subscription->user_auth_token,
            $flush
        );
        if ($result != true) {
            throw new Exception("Failed to send notification to $subscription->endpoint.");
        }
    }
}
