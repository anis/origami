<?php
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

$app->register(new ServiceControllerServiceProvider());

// Database
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'dbname'   => 'origami',
        'user'     => 'root',
        'password' => '',
    ),
));

$app->register(new DoctrineOrmServiceProvider(), array(
    'orm.proxies_dir' => __DIR__ . '/../../data/proxies',
    'orm.em.options'  => array(
        'mappings' => array(
            array(
                'type'      => 'annotation',
                'namespace' => 'Poensis\Origami\Entity',
                'path'      => __DIR__ . '/../../src/Entity',
            ),
        ),
    ),
));
