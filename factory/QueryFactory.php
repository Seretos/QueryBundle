<?php
namespace database\QueryBundle\factory;

use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\QueryBundle\interfaces\ConditionInterface;
use database\QueryBundle\interfaces\ParameterInterface;
use database\QueryBundle\parameter\StringParameter;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 05:19
 */
class QueryFactory {
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var ConditionInterface[]
     */
    private $conditions;

    public function __construct (ConnectionInterface $connection) {
        $this->connection = $connection;
        $this->conditions = [];
    }

    public function registerTypeCondition ($class) {
        $this->conditions[] = new $class();
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection () {
        return $this->connection;
    }

    /**
     * @param string      $name
     * @param mixed       $value
     * @param string|null $type
     *
     * @return ParameterInterface
     */
    public function createParameter ($name, $value, $type = null) {
        if ($type != null) {
            return new $type($this, $name, $value);
        }

        foreach ($this->conditions as $condition) {
            if ($condition->condition($value)) {
                $type = $condition->getParameterType();

                return new $type($this, $name, $value);
            }
        }

        return new StringParameter($this, $name, $value);
    }
}