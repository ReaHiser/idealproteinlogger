<?php

use IP\Controller\DefaultController;
use IP\Controller\AuthenticationController;
use IP\Controller\ClientsController;
use Symfony\Component\HttpFoundation\Request;
use Hautelook\Phpass\PasswordHash;
use Silex\Provider\SwiftmailerServiceProvider;

$config = include_once(__DIR__.'/../config/config.php');

$app['config'] = $config;
$app['debug'] = $config['debug'];

$app->register(new Silex\Provider\ServiceControllerServiceProvider())
	->register(new Silex\Provider\UrlGeneratorServiceProvider())
	->register(new Silex\Provider\FormServiceProvider())
	->register(new Silex\Provider\ValidatorServiceProvider())
	->register(new Silex\Provider\TranslationServiceProvider())
	->register(new SwiftmailerServiceProvider())
;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/../templates/',
	'twig.options' => array('debug' => $app['config']['debug']),
));

$app->register(new \Silex\Provider\SessionServiceProvider(), array(
	'session.storage.save_path' => __DIR__.'/../tmp/sessions',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.options' => array(
		'driver' => $config['db']['driver'],
		'host' => $config['db']['host'],
		'dbname' => $config['db']['dbname'],
		'user' => $config['db']['user'],
		'password' => $config['db']['password'],
	)
));

$app['default.controller'] = $app->share(function() use ($app) {
	return new DefaultController();
});

$app['auth.controller'] = $app->share(function() use ($app) {
	return new AuthenticationController($app);
});

$app['admin.clients.controller'] = $app->share(function() use ($app) {
	return new ClientsController($app);
});

$app['password.hasher'] = $app->share(function() use ($app) {
	return new PasswordHash(8,true);
});

$mustAuthenticate = function(Request $request) use ($app) {
	$request->getSession()->start();
	if(!$app['session']->has('user')) {
		return $app->redirect('/login');
	}
};

$isAdmin = function(Request $request) use ($app) {
	$user = $app['session']->get('user');
	if(!$user || $user->role != 'ROLE_ADMIN') {
		$app['session']->getFlashBag()->add('error', 'You do not have privileges for the requested page');
		return $app->redirect('/');
	}
};

$sendMail = function(Request $request) use ($app) {
    $user = $app['session']->get('user');
    if(!$user || substr($user->role, 0, 4) != 'ROLE') {
        $app['session']->getFlashBag()->add('error', 'You must login to use this page!');
        return $app->redirect('/');
    }

    $data = $form->getData();
    $message = \Swift_Message::newInstance()
        ->setSubject('Contact Form Message')
        ->setFrom(array($data['email'] => $data['name']))
        ->setTo($app['config']['contactMail'])
        ->setBody($data['comments'])
    ;

    $app['mailer']->send($message);
    $app['session']->getFlashBag()->add('success', 'Your confirmation message was sent successfully.');
};