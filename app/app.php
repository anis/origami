<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();
require __DIR__ . '/config/services.php';
require __DIR__ . '/config/routing.php';

// Decode JSON requests
$app->before(function (Request $request) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace(is_array($data) ? $data : array());
});

return $app;
