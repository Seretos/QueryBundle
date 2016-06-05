<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 05:14
 */

namespace database\QueryBundle\query;

use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\interfaces\ParameterInterface;
use PDO;

class Query {
    const FETCH_ASSOC = PDO::FETCH_ASSOC;
    const FETCH_BOTH  = PDO::FETCH_BOTH;
    const FETCH_NUM   = PDO::FETCH_NUM;

    /**
     * @var QueryFactory
     */
    private $factory;
    /**
     * @var string
     */
    private $sql;

    /**
     * @var ParameterInterface[]
     */
    private $parameters;

    /**
     * @var StatementInterface|null
     */
    private $_statement;

    /**
     * @var string
     */
    private $currentSql;

    /**
     * Query constructor.
     *
     * @param QueryFactory $factory
     * @param string       $sql
     */
    public function __construct (QueryFactory $factory, $sql) {
        $this->factory = $factory;
        $this->sql = $sql;
        $this->parameters = [];
        $this->_statement = null;
        $this->currentSql = '';
    }

    /**
     * @return string
     */
    public function getSql () {
        return $this->sql;
    }

    /**
     * @return StatementInterface
     */
    public function buildResult () {
        $newSql = $this->sql;
        foreach ($this->parameters as $parameter) {
            $newSql = $parameter->prepare($newSql);
        }

        if ($this->_statement == null || $this->currentSql != $newSql) {
            $this->_statement = $this->factory->getConnection()
                                              ->prepare($newSql);
            $this->_statement->setFetchMode(self::FETCH_ASSOC);
            $this->currentSql = $newSql;
        }

        foreach ($this->parameters as $parameter) {
            $parameter->bindParam($this->_statement);
        }
        $this->_statement->execute();

        return $this->_statement;
    }

    /**
     * @param int $mode
     *
     * @return bool
     */
    public function setFetchMode ($mode) {
        return $this->_statement->setFetchMode($mode);
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters ($parameters) {
        foreach ($parameters as $key => $param) {
            $this->setParameter($key, $param);
        }

        return $this;
    }

    /**
     * @param string      $key
     * @param string      $value
     * @param string|null $type
     *
     * @return Query
     */
    public function setParameter ($key, $value, $type = null) {
        if (!isset($this->parameters[$key])) {
            $this->parameters[$key] = $this->factory->createParameter($key, $value, $type);
        } else {
            if ($type !== null && !($this->parameters[$key] instanceof $type)) {
                $this->parameters[$key] = $this->factory->createParameter($key, $value, $type);
            }
            $this->parameters[$key]->setValue($value);
        }

        return $this;
    }
}