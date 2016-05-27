<?php
namespace database\QueryBundle\tests;

use database\DriverBundle\tests\AbstractFunctionalDatabaseTest;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\query\Query;
use PDO;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 09:46
 */
abstract class AbstractFunctionalQueryTest extends AbstractFunctionalDatabaseTest {
    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @test
     */
    public function simple () {
        $query = new Query($this->queryFactory, 'SELECT * FROM example1');
        $result = $query->buildResult();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $this->assertSame(2, $result->columnCount());
        $this->assertSame(50, $result->rowCount());
        $index = 0;
        foreach ($result as $res) {
            $this->assertEquals(['id' => $index + 1, 'info' => 'test'.$index], $res);
            $index++;
        }
    }

    /**
     * @test
     */
    public function arrayParameter () {
        $query = new Query($this->queryFactory, 'SELECT * FROM example1 WHERE id IN(:params)');

        $query->setParameter('params', [1, 2, 3]);
        $result = $query->buildResult();
        $this->assertSame(3, $result->rowCount());

        $result->next();
        $query->setParameter('params', [1, 4, 3]);
        $result2 = $query->buildResult();
        $this->assertSame(3, $result2->rowCount());
        $this->assertSame($result2,
                          $result);

        $query->setParameter('params', [1, 2, 3, 4, 5]);
        $result3 = $query->buildResult();
        $this->assertSame(5, $result3->rowCount());
        $this->assertNotSame($result3, $result);
    }
}