<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 04.06.16
 * Time: 23:13
 */

namespace database\QueryBundle\factory;


use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\QueryBundle\condition\ArrayCondition;
use database\QueryBundle\condition\BooleanCondition;
use database\QueryBundle\condition\DatetimeCondition;
use database\QueryBundle\condition\IntegerCondition;
use database\QueryBundle\condition\NullCondition;
use database\QueryBundle\condition\ResourceCondition;
use database\QueryBundle\query\Query;

class QueryBundleFactory {
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    public function __construct (ConnectionInterface $connection) {
        $this->queryFactory = new QueryFactory($connection);

        $this->registerTypeCondition(NullCondition::class);
        $this->registerTypeCondition(BooleanCondition::class);
        $this->registerTypeCondition(IntegerCondition::class);
        $this->registerTypeCondition(ResourceCondition::class);
        $this->registerTypeCondition(ArrayCondition::class);
        $this->registerTypeCondition(DatetimeCondition::class);
    }

    /**
     * @param string $class class name (require implements the ConditionInterface)
     */
    public function registerTypeCondition ($class) {
        $this->queryFactory->registerTypeCondition($class);
    }

    /**
     * @param string $sql
     * @param array  $parameters
     *
     * @return Query
     */
    public function createQuery ($sql, $parameters = []) {
        $query = new Query($this->queryFactory, $sql);
        $query->setParameters($parameters);

        return $query;
    }
}