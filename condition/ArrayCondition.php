<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 05.06.16
 * Time: 00:15
 */

namespace database\QueryBundle\condition;


use database\QueryBundle\interfaces\ConditionInterface;
use database\QueryBundle\parameter\ArrayParameter;

class ArrayCondition implements ConditionInterface{

    /**
     * @param $value
     *
     * @return boolean
     */
    public function condition ($value) {
        return is_array($value);
    }

    /**
     * @return string
     */
    public function getParameterType () {
        return ArrayParameter::class;
    }
}