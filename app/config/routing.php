<?php
use Poensis\Origami\Controller\UserController;

$app['users'] = $app->factory(function () use($app) {
    return new UserController();
});

$app->get('/users/', 'users:listAction');
$app->post('/users/', 'users:createAction');
