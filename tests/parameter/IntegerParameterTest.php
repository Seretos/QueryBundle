<?php
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\parameter\IntegerParameter;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 07:33
 */
class IntegerParameterTest extends PHPUnit_Framework_TestCase {
    /**
     * @var IntegerParameter
     */
    private $parameter;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|QueryFactory
     */
    private $factoryMock;

    protected function setUp () {
        $this->factoryMock = $this->getMockBuilder(QueryFactory::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->parameter = new IntegerParameter($this->factoryMock, 'key1', 3);
        $this->assertSame('key1', $this->parameter->getName());
        $this->assertSame(3, $this->parameter->getValue());
        $this->assertSame(PDO::PARAM_INT, $this->parameter->getType());
    }

    /**
     * @test
     */
    public function prepare () {
        $this->assertSame('SELECT * FROM test WHERE key = :key1',
                          $this->parameter->prepare('SELECT * FROM test WHERE key = :key1'));
    }

    /**
     * @test
     */
    public function bindParam () {
        /* @var $statement PHPUnit_Framework_MockObject_MockObject|StatementInterface */
        $statement = $this->getMockBuilder(StatementInterface::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $statement->expects($this->once())
                  ->method('bindParam')
                  ->with('key1', 3, PDO::PARAM_INT);

        $this->parameter->bindParam($statement);
    }
}