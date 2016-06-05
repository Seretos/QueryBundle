<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 04.06.16
 * Time: 23:58
 */

namespace database\QueryBundle\condition;


use database\QueryBundle\interfaces\ConditionInterface;
use database\QueryBundle\parameter\DatetimeParameter;

class DatetimeCondition implements ConditionInterface {

    /**
     * @param $value
     *
     * @return boolean
     */
    public function condition ($value) {
        return $value instanceof \DateTime;
    }

    public function getParameterType () {
        return DatetimeParameter::class;
    }
}