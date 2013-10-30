<?php

namespace PhpORM\Mapper;

class MapperAbstract
{
    protected $db;
    protected $entityClass;
    protected $table;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function delete(array $where)
    {
        $db = $this->getDb();

        $db->delete($this->table, $where);
    }

    /**
     * @param array $orderby
     * @param string $sort
     * @return array
     */
    public function fetchAll(array $orderby = null, $sort = 'ASC')
    {
        $db = $this->getDb();

        if(!is_null($orderby)) {
            $orderby = " ORDER BY " . implode(', ', $orderby);
        }
        $sql = "SELECT * FROM `"  . $this->table . $orderby . ' ' . $sort;
        $result = $db->fetchAll($sql);
        
        $data = array();
        foreach($result as $row) {
        	$class = $this->entityClass;
        	$data[] = new $class($row);
        }

        return $data;
    }

    public function find($id)
    {
        $db = $this->getDb();

        $sql = "SELECT * FROM `"  . $this->table . "` WHERE id = :id ORDER BY id ASC";
        $result = $db->fetchAssoc($sql, array('id' => $id));

        $class = $this->entityClass;
        return new $class($result);
    }


    /**
     * @return \PDO
     */
    public function getDb()
    {
        return $this->db;
    }

    public function save($entity)
    {
        $db = $this->getDb();
        $table = '`'.$this->table.'`';
        $objectData = $entity->toArray();
        foreach($objectData as $key => $value) {
            if(is_object($value) && get_class($value) == 'DateTime') {
                $objectData[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        if($entity->id) {
            $db->update($table, $objectData, array('id' => $entity->id));
        } else {
            $db->insert($table, $objectData);
            $entity->id = $db->lastInsertId();
        }
    }
}