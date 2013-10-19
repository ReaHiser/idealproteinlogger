<?php

namespace IP\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use IP\Form\OrderForm;
use IP\Mapper\UserMapper;
use IP\Form\ClientForm;

class DefaultController
{
	public function contactAction(Request $request, Application $app)
	{
		$form = new ContactForm($app['form.factory']);
		$contactForm = $form->build();
		
		if($request->isMethod('POST')) {
			$contactForm->bind($request);
			if($contactForm->isValid()) {
				$data = $contactForm->getData();
				$message = \Swift_Message::newInstance()
					->setSubject('Contact Form Message')
					->setFrom(array($data['email'] => $data['name']))
					->setTo($app['config']['contactMail'])
					->setBody($data['comments'])
				;
				
				$app['mailer']->send($message);
				$app['session']->getFlashBag()->add('success', 'Your message has been sent. Thank you!');
				
				// Clear the form out
				$contactForm = $form->build();
			} else {
				$app['session']->getFlashBag()->add('error', 'There was a problem with your submission. Please check below.');
			}
		}
		
		return $app['twig']->render('Default/contact.html.twig', array(
			'contactForm' => $contactForm->createView(),
		));
	}
	
	public function indexAction(Application $app)
	{
		return $app['twig']->render('Default/index.html.twig');
	}
	
	public function myAccountAction(Request $request, Application $app)
	{
		$userMapper = new UserMapper($app['db']);
		$user = $app['session']->get('user');
		
		$form = new ClientForm($app['form.factory']);
		$clientForm = $form->build($user->toArray(), array('passwordRequired' => false));
		
		$clientForm->remove('username');
		$clientForm->remove('role');
		$clientForm->remove('sharefile');
        $clientForm->remove('cpo2');
        $clientForm->remove('cpo3');
        $clientForm->remove('paychoice');

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
				$app['session']->getFlashBag()->add('success', 'Your account has been updated');
			}
		}
		
		return $app['twig']->render('Default/myAccount.html.twig', array(
			'clientForm' => $clientForm->createView(),
		));
	}
	
	public function orderPageAction(Request $request, Application $app)
	{
        $form = new OrderForm($app['form.factory']);
        $orderForm = $form->build();

        if($request->isMethod('POST')) {
            $orderForm->bind($request);
            if($orderForm->isValid()) {
                $data = $orderForm->getData();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact Form Message')
                    ->setFrom(array($data['email'] => $data['name']))
                    ->setTo($app['config']['contactMail'])
                    ->setBody($data['comments'])
                ;

                $app['mailer']->send($message);
                $app['session']->getFlashBag()->add('success', 'Your message has been sent. Thank you!');

                // Clear the form out
                $orderForm = $form->build();
            } else {
                $app['session']->getFlashBag()->add('error', 'There was a problem with your submission. Please check below.');
            }
        }
		
		return $app['twig']->render('Default/orderPage.html.twig', array(
            'orderForm' => $orderForm->createView(),
        ));
	}
}