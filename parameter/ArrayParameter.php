<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 05:52
 */

namespace database\QueryBundle\parameter;


use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\interfaces\ParameterInterface;

class ArrayParameter extends AbstractParameter {
    /**
     * ArrayParameter constructor.
     *
     * @param QueryFactory $factory
     * @param              $name
     * @param              $value
     */
    public function __construct (QueryFactory $factory, $name, $value) {
        parent::__construct($factory, $name, $value);
        $this->setValue($value);
    }

    /**
     * @param array $value
     */
    public function setValue ($value) {
        $subParameters = [];
        foreach ($value as $key => $val) {
            $type = null;
            if (is_array($val)) {
                $type = $val['type'];
                $val = $val['value'];
            }
            $subParameters[] = $this->factory->createParameter($this->getName().'_'.$key, $val, $type);
        }
        parent::setValue($subParameters);
    }

    /**
     * @param string $sql
     *
     * @return mixed
     */
    public function prepare ($sql) {
        return str_replace(':'.$this->getName(),
                           implode(',',
                                   array_map(function (ParameterInterface $item) {
                                       return ':'.$item->getName();
                                   },
                                       $this->getValue())),
                           $sql);
    }

    /**
     * @param StatementInterface $statement
     *
     * @return bool
     */
    public function bindParam (StatementInterface $statement) {
        $values = $this->getValue();
        foreach ($values as $value) {
            /* @var $value ParameterInterface */
            $value->bindParam($statement);
        }

        return true;
    }
}