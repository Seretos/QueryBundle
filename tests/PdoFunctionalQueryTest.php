<?php
use database\DriverBundle\connection\pdo\PdoConnection;
use database\QueryBundle\factory\QueryBundleFactory;
use database\QueryBundle\tests\AbstractFunctionalQueryTest;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 10:34
 */
class PdoFunctionalQueryTest extends AbstractFunctionalQueryTest {
    protected function setUp () {
        parent::setUp();
        $this->connection = new PdoConnection(self::CONFIG['host'],
                                              self::CONFIG['user'],
                                              self::CONFIG['password'],
                                              self::CONFIG['database']);

        $this->queryFactory = new QueryBundleFactory($this->connection);
    }
}