<?php
// Routes:

$app->get('/', 'App\Controllers\HomeController:index');
$app->get('/home', 'App\Controllers\HomeController:index');

$app->get('/admin', 'App\Controllers\AdminController:index');

$app->group('/api', function () {
    $this->group('/push-messages', function () {
        $this->post('', 'App\Controllers\Api\PushMessagesController:post');
    });
    $this->group('/push-subscriptions', function () {
        $this->get('', 'App\Controllers\Api\PushSubscriptionsController:getAll');
        $this->post('', 'App\Controllers\Api\PushSubscriptionsController:post');
        $this->delete('/{key}', 'App\Controllers\Api\PushSubscriptionsController:delete');
    });
});
