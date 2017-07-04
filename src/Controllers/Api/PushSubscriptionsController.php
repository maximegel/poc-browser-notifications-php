<?php
namespace App\Controllers\Api;

use Jgut\Slim\Controller\Base as Controller;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class PushSubscriptionsController extends Controller
{
    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            return $response
              ->withHeader('Content-Type', 'application/json')
              ->withJson($this->parsePushSubscriptionsToOutputFormat($this->getPushSubscriptions()));
        } catch (Exception $e) {
            return $response->withStatus(500);
        }
    }

    // TODO(maximegelinas): Validate received subscription.
    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withJson($this->addPushSubscription($this->parsePushSubscriptionToStoreFormat($request->getParsedBody())));
        } catch (Exception $e) {
            return $response->withStatus(500);
        }
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $this->removePushSubscription($args['key']);
        } catch (Exception $e) {
            return $response->withStatus(500);
        }
    }

    private function getPushSubscriptions()
    {
        return $this->pushSubscriptionStore->findAll();
    }

    private function addPushSubscription($subscription)
    {
        $this->pushSubscriptionStore->endpoint = $subscription['endpoint'];
        $this->pushSubscriptionStore->user_public_key = $subscription['userPublicKey'];
        $this->pushSubscriptionStore->user_auth_token = $subscription['userAuthToken'];
        $this->pushSubscriptionStore->save();

        return $subscription;
    }

    private function removePushSubscription($subscriptionUserPublicKey)
    {
        $this->pushSubscriptionStore
            ->where('user_public_key', '=', $subscriptionUserPublicKey)
            ->find()
            ->delete();
    }

    private function parsePushSubscriptionsToOutputFormat($subscriptionListFromStore)
    {
        $result = [];
        foreach ($subscriptionListFromStore as $subscription) {
            array_push($result, $this->parsePushSubscriptionToOutputFormat($subscription));
        }
        return $result;
    }

    private function parsePushSubscriptionToOutputFormat($subscriptionFromStore)
    {
        return [
            'id' => $subscriptionFromStore->id,
            'endpoint' => $subscriptionFromStore->endpoint,
            'keys' => [
                'p256dh' => $subscriptionFromStore->user_public_key,
                'auth' => $subscriptionFromStore->user_auth_token
            ]
        ];
    }

    private function parsePushSubscriptionToStoreFormat($subscriptionFromRequest)
    {
        return [
            'endpoint' => $subscriptionFromRequest['endpoint'],
            'userPublicKey' => $subscriptionFromRequest['keys']['p256dh'],
            'userAuthToken' => $subscriptionFromRequest['keys']['auth']
        ];
    }
}
