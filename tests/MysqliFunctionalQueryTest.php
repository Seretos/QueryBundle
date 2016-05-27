<?php
use database\DriverBundle\connection\mysqli\MysqliConnection;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\tests\AbstractFunctionalQueryTest;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 10:33
 */
class MysqliFunctionalQueryTest extends AbstractFunctionalQueryTest {
    protected function setUp () {
        parent::setUp();
        $mysqli = new mysqli(self::CONFIG['host'],
                             self::CONFIG['user'],
                             self::CONFIG['password'],
                             self::CONFIG['database']);

        $this->connection = new MysqliConnection($mysqli);

        $this->queryFactory = new QueryFactory($this->connection);
    }
}