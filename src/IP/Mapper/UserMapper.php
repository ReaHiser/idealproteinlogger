<?php

namespace IP\Mapper;

use PhpORM\Mapper\MapperAbstract;
use IP\Entity\User;

class UserMapper extends MapperAbstract
{
	protected $table = 'user';
	protected $entityClass = 'IP\Entity\User';
	
	public function fetchViaAuth($username, $password, $hasher)
	{
		$db = $this->getDb();
		$sql = "SELECT * FROM ".$this->table." WHERE username = :username";
		$row = $db->fetchAssoc($sql, array(
				'username' => $username,
		));
	
		$check = $hasher->CheckPassword($password, $row['password']);
	
		if($check) {
			$class = $this->entityClass;		
			return new $class($row);
		}
	
		return null;
	}
}