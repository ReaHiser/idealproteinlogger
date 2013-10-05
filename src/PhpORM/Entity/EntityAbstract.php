<?php

namespace PhpORM\Entity;

abstract class EntityAbstract
{
    public function __construct(array $data = array()) {
        if(!empty($data)) {
            $this->fromArray($data);
        }
    }
    public function __call($name, $arguments)
    {
        if(property_exists($this, $name)) {
            return $this->$name;
        }
    }

    public function __get($key)
    {
        if(property_exists($this, $key)) {
            return $this->$key;
        }
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function fromArray($data)
    {
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function toArray()
    {
        $objectData = get_object_vars($this);

        return $objectData;
    }
}