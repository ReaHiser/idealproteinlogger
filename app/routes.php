<?php

$app->get('/', 'default.controller:indexAction')->bind('homepage')->before($mustAuthenticate);
$app->match('/contact', 'default.controller:contactAction')->bind('contact')->method('GET|POST');
$app->match('/login', 'auth.controller:loginAction')->bind('login')->method('GET|POST');
$app->get('/logout', 'auth.controller:logoutAction')->bind('logout');
$app->match('/myaccount', 'default.controller:myAccountAction')->bind('my_account')->method('GET|POST');
$app->get('/order_page', 'default.controller:orderPageAction')->bind('order_page')->method('GET|POST');

$app->match('/admin/clients', 'admin.clients.controller:indexAction')->bind('admin_clients_index')->method('POST|GET')->before($mustAuthenticate)->before($isAdmin);
$app->match('/admin/clients/create', 'admin.clients.controller:createAction')->bind('admin_clients_create')->method('POST|GET')->before($mustAuthenticate)->before($isAdmin);
$app->match('/admin/clients/update/{clientID}', 'admin.clients.controller:updateAction')->bind('admin_clients_update')->method('POST|GET')->before($mustAuthenticate)->before($isAdmin)->after($sendMail);