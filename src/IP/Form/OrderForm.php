<?php

namespace IP\Form;

use PhpORM\Mapper\MapperAbstract;
use Symfony\Component\Validator\Constraints as Assert;

class OrderForm extends FormAbstract
{
    /**
     *
     * @var MapperAbstract
     */
    protected $orderMapper;

	public function build($data = array(), $options = array())
	{
        $allProducts = $this->getOrderMapper()->fetchProduct();
		$form = $this->factory->createBuilder('form', $data);
        foreach($allProducts as $value) {
            $form->add('product', 'text', array(
                    'label'       => 'Product',
                    'data'        => $value['product']
                ))
                ->add('quantity', 'text', array(
                    'label'       => 'Quantity',
                    'data'        => 0,
                    'attr'        => array('style' => 'text-align: right;')
                ))
            ;
        }
		return $form->getForm();
	}

    /**
     * Returns the order mapper
     *
     * @throws \Exception
     * @return \PhpORM\Mapper\MapperAbstract
     */
    public function getOrderMapper()
    {
        if($this->orderMapper == null) {
            throw new \Exception('Please set the order mapper');
        }

        return $this->orderMapper;
    }

    public function setOrderMapper(MapperAbstract $mapper)
    {
        $this->orderMapper = $mapper;
    }
}