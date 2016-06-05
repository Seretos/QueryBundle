<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 05.06.16
 * Time: 00:08
 */

namespace database\QueryBundle\condition;


use database\QueryBundle\interfaces\ConditionInterface;
use database\QueryBundle\parameter\BooleanParameter;

class BooleanCondition implements ConditionInterface {

    /**
     * @param $value
     *
     * @return boolean
     */
    public function condition ($value) {
        return is_bool($value);
    }

    /**
     * @return string
     */
    public function getParameterType () {
        return BooleanParameter::class;
    }
}