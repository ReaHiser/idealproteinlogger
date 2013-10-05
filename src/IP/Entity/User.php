<?php

namespace IP\Entity;

use PhpORM\Entity\EntityAbstract;

class User extends EntityAbstract
{
    protected $full_name;
	protected $username;
	protected $password;
	protected $role;
	protected $email;
}