<?php

namespace IP\Controller;

use Silex\Application;
use IP\Form\ClientForm;
use Symfony\Component\HttpFoundation\Request;
use IP\Entity\User;
use IP\Mapper\UserMapper;
use IP\Form\UserSelectForm;

class ClientsController
{
	public function createAction(Request $request, Application $app)
	{
		$error = '';
		$form = new ClientForm($app['form.factory']);
		$clientForm = $form->build();
		
		if($request->isMethod('POST')) {
			$clientForm->bind($request);
			if($clientForm->isValid()) {
				$user = new User($clientForm->getData());
				$hasher = $app['password.hasher'];
				$user->password = $hasher->HashPassword($user->password);
				$userMapper = new UserMapper($app['db']);
				$userMapper->save($user);
				
				$app['session']->getFlashBag()->add('success', 'Client created');
				return $app->redirect($app['url_generator']->generate('homepage'));
			} else {
				$app['session']->getFlashBag()->add('error', 'There was an error creating the client. Please check below.');
			}
		}
		
		return $app['twig']->render('Admin/Clients/create.html.twig', array(
			'error' => $error,
			'form' => $clientForm->createView(),
		));
	}

	public function indexAction(Request $request, Application $app)
	{
		$form = new UserSelectForm($app['form.factory']);
		$form->setUserMapper(new UserMapper($app['db']));
		$selectForm = $form->build();
		
		if($request->isMethod('POST')) {
			$selectForm->bind($request);
			if($selectForm->isValid()) {
				$data = $selectForm->getData();
				return $app->redirect($app['url_generator']->generate('admin_clients_update', array('clientID' => $data['username'])));
			} else {
				$app['session']->getFlashBag()->add('error', 'The user that was selected was not in the drop down list. Please select a user from below.');
			}
		}
		
		return $app['twig']->render('Admin/Clients/index.html.twig', array(
			'form' => $selectForm->createView(),
		));
	}
	
	public function updateAction(Request $request, Application $app, $clientID)
	{
		$userMapper = new UserMapper($app['db']);
		$user = $userMapper->find($clientID);
		
		$form = new UserSelectForm($app['form.factory']);
		$form->setUserMapper($userMapper);
		$selectForm = $form->build(array('username' => $clientID));
		
		$form = new ClientForm($app['form.factory']);
		$clientForm = $form->build($user->toArray(), array('passwordRequired' => false, 'disableUsername' => true));
		
		if($request->isMethod('POST')) {
			$clientForm->bind($request);
			if($clientForm->isValid()) {
				$data = $clientForm->getData();
				if(empty($data['password'])) {
					unset($data['password']);
				} else {
					$hasher = $app['password.hasher'];
					$data['password']= $hasher->HashPassword($data['password']);
				}
				
				$user->fromArray($data);
				
				$userMapper->save($user);
				$app['session']->getFlashBag()->add('success', 'Client was updated successfully');
				return $app->redirect($app['url_generator']->generate('admin_clients_index'));
			}
		}
		
		return $app['twig']->render('Admin/Clients/update.html.twig', array(
				'selectForm' => $selectForm->createView(),
				'clientForm' => $clientForm->createView(),
		));
	}
}