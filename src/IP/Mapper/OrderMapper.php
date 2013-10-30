<?php

namespace IP\Mapper;

use PhpORM\Mapper\MapperAbstract;
use IP\Entity\Order;

class OrderMapper extends MapperAbstract
{
	protected $table = 'fooditems';
	protected $entityClass = 'IP\Entity\Order';
	
	public function fetchProduct()
	{
		$db = $this->getDb();
		$sql = "SELECT name AS product FROM " . $this->table;
		$row = $db->fetchAssoc($sql);

		$class = $this->entityClass;
        return new $class($row);
	}
}