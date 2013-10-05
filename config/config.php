<?php

$env = getenv('SILEX_ENV');
$env = strtolower($env);

// Production server
$config = array(
    'db' => array(
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'dbname' => 'idealprotein',
        'user' => 'root',
        'password' => 'MyName!1987',
    ),
    'debug' => false,
	'contactMail' => array('admin@manzwebdesigns.com'),
);

// QA Server
if($env == 'qa') {
    // Not used right now
}

// Dev Server
if($env == 'dev') {
    $config['debug'] = true;
    $config['db']['host'] = 'localhost';
    $config['db']['user'] = 'dpc';
    $config['db']['password'] = '%cpak>v}e41ND:#~';
    $config['db']['dbname'] = 'dpc';

    $config['debug'] = true;
    
    $config['contactMail'] = array('admin@manzwebdesigns.com');
}

// Local development server
if($env == 'local') {
    $config['debug'] = true;
    $config['db']['host'] = 'localhost';
    $config['db']['user'] = 'root';
    $config['db']['password'] = 'MyName!1987';
    $config['db']['dbname'] = 'idealprotein';

    $config['debug'] = true;
    
    $config['contactMail'] = array('admin@manzwebdesigns.com');
}

// Local development server
if($env == 'vagrant') {
	$config['debug'] = true;
	$config['db']['host'] = 'localhost';
	$config['db']['user'] = 'root';
	$config['db']['password'] = 'vagrant';
	$config['db']['dbname'] = 'main';

	$config['debug'] = true;
	
	$config['contactMail'] = array('chris@tankws.com');
}

return $config;
