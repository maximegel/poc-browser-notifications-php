<?php
use Lazer\Classes\Database;
use Lazer\Classes\LazerException;
use Lazer\Classes\Helpers\Validate;

define('LAZER_DATA_PATH', $app->getContainer()->get('settings')['data']['dir']);

try {
    Validate::table('push_subscriptions')->exists();
} catch (LazerException $e) {
    Database::create('push_subscriptions', array(
        'id' => 'integer',
        'endpoint' => 'string',
        'user_public_key' => 'string',
        'user_auth_token' => 'string'
    ));
}
