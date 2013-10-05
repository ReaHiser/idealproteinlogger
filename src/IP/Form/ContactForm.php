<?php

namespace IP\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ContactForm extends FormAbstract
{
	public function build($data = array(), $options = array())
	{
		$form = $this->factory->createBuilder('form', $data)
			->add('name', 'text', array(
				'label'       => 'Your Name',
				'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)))
			))
			->add('email', 'email', array(
				'label'       => 'Email Address',
				'constraints' => array(new Assert\NotBlank())
			))
			->add('comments', 'textarea', array(
				'label'       => 'Comments',
			))
		;
		
		return $form->getForm();
	}
}