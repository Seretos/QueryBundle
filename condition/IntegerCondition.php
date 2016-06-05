<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 05.06.16
 * Time: 00:10
 */

namespace database\QueryBundle\condition;


use database\QueryBundle\interfaces\ConditionInterface;
use database\QueryBundle\parameter\IntegerParameter;

class IntegerCondition implements ConditionInterface {

    /**
     * @param $value
     *
     * @return boolean
     */
    public function condition ($value) {
        return is_integer($value);
    }

    /**
     * @return string
     */
    public function getParameterType () {
        return IntegerParameter::class;
    }
}