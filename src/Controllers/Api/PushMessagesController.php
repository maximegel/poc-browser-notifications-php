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
        } catch (\Exception $e) {
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->withJson([
                    'developperMessage' => $e->getMessage(),
                    'userMessage' => 'Failed to send notification.'
                ]);
        }
    }

    private function triggerPushMessages($message)
    {
        foreach ($this->pushSubscriptionStore->findAll() as $subscription) {
            $this->triggerPushMessage($message, $subscription, false);
        }
        // TODO(maximegelinas): Extract this logic in a dedicated message pusher class.
        $result = $this->messagePusher->flush();
        if ($result !== true && is_array($result) && count($result) >= 1) {
            throw new \Exception($result[0]['message']);
        }
    }

    private function triggerPushMessage($message, $subscription, $flush = true)
    {
        $this->messagePusher->sendNotification(
            $subscription->endpoint,
            json_encode($message),
            $subscription->user_public_key,
            $subscription->user_auth_token,
            false
        );
        // TODO(maximegelinas): Extract this logic in a dedicated message pusher class.
        $result = $this->messagePusher->flush();
        if ($result !== true && is_array($result) && count($result) >= 1) {
            throw new \Exception($result[0]['message']);
        }
    }
}
