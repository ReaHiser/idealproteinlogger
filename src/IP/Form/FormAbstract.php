<?php

namespace IP\Form;

abstract class FormAbstract
{
    protected $factory;

    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    /**
     * Generates the form object
     * 
     * @param array $data
     * @param array $options
     * return Symfony\Component\Form\Form
     */
	abstract public function build($data = array(), $options = array());
}