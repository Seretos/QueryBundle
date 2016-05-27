<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 05:29
 */

namespace database\QueryBundle\interfaces;


use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;

interface ParameterInterface {
    public function __construct (QueryFactory $factory, $name, $value);

    /**
     * @return string
     */
    public function getName ();

    /**
     * @return mixed
     */
    public function getValue ();

    /**
     * @return mixed
     */
    public function getType ();

    /**
     * @param mixed $value
     */
    public function setValue ($value);

    /**
     * @param string $sql
     *
     * @return string
     */
    public function prepare ($sql);

    /**
     * @param StatementInterface $statement
     *
     * @return boolean
     */
    public function bindParam (StatementInterface $statement);
}