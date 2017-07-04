<?php
// DIC configuration:

$container = $app->getContainer();

// View renderer.
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['views_path']);
};

// Monolog.
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Register stores.
$container['pushSubscriptionStore'] = function ($c) {
    return Lazer\Classes\Database::table('push_subscriptions');
};

// Message pusher.
$container['messagePusher'] = function ($c) {
    $settings = $c->get('settings')['push_messaging'];
    return new Minishlink\WebPush\WebPush($settings['auth']);
};

// Register controllers.
$controllers = [
    'App\Controllers\HomeController',
    'App\Controllers\AdminController',
    'App\Controllers\Api\PushMessagesController',
    'App\Controllers\Api\PushSubscriptionsController'
];
foreach (Jgut\Slim\Controller\Resolver::resolve($controllers) as $controller => $callback) {
    $container[$controller] = $callback;
}
