<?php
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\exception\ParameterException;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\parameter\DatetimeParameter;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 08:27
 */
class DatetimeParameterTest extends PHPUnit_Framework_TestCase {
    /**
     * @var DatetimeParameter
     */
    private $parameter;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|QueryFactory
     */
    private $factoryMock;

    /**
     * @var DateTime
     */
    private $date;

    protected function setUp () {
        $this->factoryMock = $this->getMockBuilder(QueryFactory::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $this->date = new DateTime();

        $this->parameter = new DatetimeParameter($this->factoryMock, 'key1', $this->date);
        $this->assertSame('key1', $this->parameter->getName());
        $this->assertSame($this->date, $this->parameter->getValue());
        $this->assertSame(PDO::PARAM_STR, $this->parameter->getType());
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
                  ->with('key1', $this->date->format('Y-m-d H:i:s'), PDO::PARAM_STR);

        $this->parameter->bindParam($statement);
    }

    /**
     * @test
     */
    public function invalid () {
        $this->parameter = new DatetimeParameter($this->factoryMock, 'key1', 'test1');

        /* @var $statementMock PHPUnit_Framework_MockObject_MockObject|StatementInterface */
        $statementMock = $this->getMockBuilder(StatementInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $statementMock->expects($this->never())
                      ->method('bindParam');

        $this->setExpectedExceptionRegExp(ParameterException::class);
        $this->parameter->bindParam($statementMock);
    }
}