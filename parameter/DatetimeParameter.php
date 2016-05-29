<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 08:21
 */

namespace database\QueryBundle\parameter;


use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\exception\ParameterException;

class DatetimeParameter extends AbstractParameter {
    /**
     * @param StatementInterface $statement
     *
     * @return bool
     * @throws ParameterException
     */
    public function bindParam (StatementInterface $statement) {
        $date = $this->getValue();
        if (!($date instanceof \DateTime)) {
            throw new ParameterException('invalid datetime parameter!');
        }

        return $statement->bindValue($this->getName(), $date->format('Y-m-d H:i:s'), $this->getType());
    }
}