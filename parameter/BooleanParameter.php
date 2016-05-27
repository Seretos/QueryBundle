<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 07:21
 */

namespace database\QueryBundle\parameter;


class BooleanParameter extends AbstractParameter {
    /**
     * @return int
     */
    public function getType () {
        return \PDO::PARAM_BOOL;
    }
}