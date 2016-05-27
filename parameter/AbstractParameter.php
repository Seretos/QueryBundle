<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 05:30
 */

namespace database\QueryBundle\parameter;


use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\interfaces\ParameterInterface;

abstract class AbstractParameter implements ParameterInterface {
    /**
     * @var string
     */
    private $name;
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var QueryFactory
     */
    protected $factory;

    /**
     * AbstractParameter constructor.
     *
     * @param QueryFactory $factory
     * @param              $name
     * @param              $value
     */
    public function __construct (QueryFactory $factory, $name, $value) {
        $this->factory = $factory;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue () {
        return $this->value;
    }

    public function getType () {
        return \PDO::PARAM_STR;
    }

    /**
     * @param mixed $value
     */
    public function setValue ($value) {
        $this->value = $value;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function prepare ($sql) {
        return $sql;
    }

    /**
     * @param StatementInterface $statement
     *
     * @return bool
     */
    public function bindParam (StatementInterface $statement) {
        return $statement->bindParam($this->getName(), $this->getValue(), $this->getType());
    }
}