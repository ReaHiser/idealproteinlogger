#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\DialogHelper;
use Hautelook\Phpass\PasswordHash;

use IP\Mapper\UserMapper;
use IP\Entity\User;

$config = include_once __DIR__.'/../config/config.php';
$app = new Application();
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => $config['db']['driver'],
        'host' => $config['db']['host'],
        'dbname' => $config['db']['dbname'],
        'user' => $config['db']['user'],
        'password' => $config['db']['password'],
    )
));

$console = new ConsoleApplication('IP Console Application', '0.1');

$console->register('create-user')
    ->setDefinition(array(
        new InputOption('username', '', InputOption::VALUE_REQUIRED, 'Username for new user'),
    ))
    ->setDescription('Generates a new user')
    ->setHelp('Usage: <info>console.php create-user --username=[username]</info>')
    ->setCode(function(InputInterface $input, OutputInterface $output) use($app) {
        $username = $input->getOption('username');

        $dialog = new DialogHelper();
        $match = false;
        while(!$match) {
            $password = $dialog->askHiddenResponse($output, 'What is the password? ');
            $confPassword = $dialog->askHiddenResponse($output, 'Please confirm the password: ');

            if($password == $confPassword) {
                $match = true;
            } else {
                $output->writeln('Passwords do not match, try again');
            }
        }

        $hasher = new PasswordHash(8,true);
        $hash = $hasher->HashPassword($password);

        $user = new User();
        $userMapper = new UserMapper($app['db']);
        $user->fromArray(array(
            'username' => $username,
            'password' => $hash,
        ));
        $userMapper->save($user);

        if($user->id > 0) {
            $output->writeln('User has been created');
        } else {
            $output->writeln('Failed to create user');
        }

    })
;

$console->run();
