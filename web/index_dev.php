<?php

require_once __DIR__.'/../vendor/autoload.php';

Symfony\Component\Debug\Debug::enable();

$app = new Silex\Application();

require __DIR__.'/../app/config/dev.php';
require __DIR__.'/../app/app.php';

//require __DIR__.'/../src/controllers.php';

$app->run();
