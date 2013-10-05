<?php

namespace IP\Form;

use PhpORM\Mapper\MapperAbstract;
use Symfony\Component\Validator\Constraints as Assert;

class UserSelectForm extends FormAbstract
{
	/**
	 * 
	 * @var MapperAbstract
	 */
	protected $userMapper;
	
	public function build($data = array(), $options = array())
	{
		$allUsers = $this->getUserMapper()->fetchAll();
		$allUserNames = array();
		foreach($allUsers as $user) {
			$allUserNames[$user->id] = $user->username;
		}
		
		$form = $this->factory->createBuilder('form', $data)
			->add('username', 'choice', array(
				'label'       => 'Username',
				'choices'     => $allUserNames,
				'empty_value' => 'Choose a User',
				'constraints' => array(new Assert\NotBlank())
			))
		;
			
			return $form->getForm();
	}
	
	/**
	 * Returns the user mapper
	 * 
	 * @throws Exception
	 * @return \PhpORM\Mapper\MapperAbstract
	 */
	public function getUserMapper()
	{
		if($this->userMapper == null) {
			throw new \Exception('Please set the user mapper');
		}
		
		return $this->userMapper;
	}
	
	public function setUserMapper(MapperAbstract $mapper)
	{
		$this->userMapper = $mapper;
	}
}