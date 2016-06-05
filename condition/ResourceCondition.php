<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 05.06.16
 * Time: 00:13
 */

namespace database\QueryBundle\condition;


use database\QueryBundle\interfaces\ConditionInterface;
use database\QueryBundle\parameter\ResourceParameter;

class ResourceCondition implements ConditionInterface {

    /**
     * @param $value
     *
     * @return boolean
     */
    public function condition ($value) {
        return is_resource($value);
    }

    /**
     * @return string
     */
    public function getParameterType () {
        return ResourceParameter::class;
    }
}