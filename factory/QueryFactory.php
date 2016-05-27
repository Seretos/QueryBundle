<?php
namespace database\QueryBundle\factory;

use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\interfaces\ParameterInterface;
use database\QueryBundle\parameter\ArrayParameter;
use database\QueryBundle\parameter\BooleanParameter;
use database\QueryBundle\parameter\DatetimeParameter;
use database\QueryBundle\parameter\IntegerParameter;
use database\QueryBundle\parameter\NullParameter;
use database\QueryBundle\parameter\ResourceParameter;
use database\QueryBundle\parameter\StringParameter;
use database\QueryBundle\result\Result;
use database\QueryBundle\result\ResultIterator;

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

    public function __construct (ConnectionInterface $connection) {
        $this->connection = $connection;
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

        if ($value === null) {
            return new NullParameter($this, $name, $value);
        } else if (is_bool($value)) {
            return new BooleanParameter($this, $name, $value);
        } else if (is_int($value)) {
            return new IntegerParameter($this, $name, $value);
        } else if (is_resource($value)) {
            return new ResourceParameter($this, $name, $value);
        } else if (is_array($value)) {
            return new ArrayParameter($this, $name, $value);
        } else if ($value instanceof \DateTime) {
            return new DatetimeParameter($this, $name, $value);
        }

        return new StringParameter($this, $name, $value);
    }
}