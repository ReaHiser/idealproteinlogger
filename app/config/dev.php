<?php

$app['debug'] = true;

$app['db.options'] = array(
    'driver' => 'pdo_mysql',
    'host' => '127.0.0.1',
    'dbname' => 'idealprotein',
    'user' => 'root',
    'password' => 'MyName!1987',
	'charset' => 'utf8',
	'port' => 3306,
);

$app['twig.options.cache'] = __DIR__ . '/../../cache';
