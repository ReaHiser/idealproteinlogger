<?php

namespace IP\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ClientForm extends FormAbstract
{
	public function build($data = array(), $options = array())
	{
		$passwordRequired = true;
        $disableUsername = false;

		if(isset($options['passwordRequired'])) {
			$passwordRequired = $options['passwordRequired'];
		}

        if(isset($options['disableUsername'])) {
            $disableUsername = $options['disableUsername'];
        }

        if(isset($data['sharefile'])) {
            $data['sharefile'] = (bool) $data['sharefile'];
        };

        if(isset($data['cpo2'])) {
            $data['cpo2'] = (bool) $data['cpo2'];
        };

		if(isset($data['cpo3'])) {
			$data['cpo3'] = (bool) $data['cpo3'];
		};

        if(isset($data['paychoice'])) {
            $data['paychoice'] = (bool) $data['paychoice'];
        };

		$form = $this->factory->createBuilder('form', $data)
			->add('full_name', 'text', array(
				'label'       => 'Full Name' /*,
				'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3))) */
			))
			->add('username', 'text', array(
				'label'       => 'Username',
				'constraints' => array(new Assert\NotBlank()),
                'disabled' => $disableUsername,
			))
			->add('password', 'password', array(
				'label'       => 'Password',
				'required' 	  => $passwordRequired,
			))
			->add('role', 'choice', array(
				'label'       => 'User Role',
				'choices'     => array('ROLE_ADMIN' => 'Admin', 'ROLE_USER' => 'Client'),
				'expanded'    => false,
				'empty_value' => 'Choose a Role',
				'constraints' => new Assert\Choice(array('ROLE_ADMIN', 'ROLE_USER')),
			))
			->add('email', 'email', array(
				'label'       => 'Email Address',
				'constraints' => new Assert\Email()
			))
		;

		return $form->getForm();
	}
}