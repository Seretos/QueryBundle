<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 05.06.16
 * Time: 00:06
 */

namespace database\QueryBundle\condition;


use database\QueryBundle\interfaces\ConditionInterface;
use database\QueryBundle\parameter\NullParameter;

class NullCondition implements ConditionInterface {

    /**
     * @param $value
     *
     * @return boolean
     */
    public function condition ($value) {
        return $value === null;
    }

    /**
     * @return string
     */
    public function getParameterType () {
        return NullParameter::class;
    }
}