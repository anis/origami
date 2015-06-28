<?php
use Poensis\Origami\Controller\UserController;

$app['users'] = $app->factory(function () {
    return new UserController();
});

$app->get('/users/', 'users:listAction');
