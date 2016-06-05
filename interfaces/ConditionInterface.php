<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 04.06.16
 * Time: 23:55
 */

namespace database\QueryBundle\interfaces;


interface ConditionInterface {
    /**
     * @param $value
     *
     * @return boolean
     */
    public function condition ($value);

    /**
     * @return string
     */
    public function getParameterType ();
}