<?php
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\parameter\AbstractParameter;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 06:49
 */
class AbstractParameterTest extends PHPUnit_Framework_TestCase {
    /**
     * @var AbstractParameter|PHPUnit_Framework_MockObject_MockObject
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

        $this->parameter = $this->getMockBuilder(AbstractParameter::class)
                                ->setConstructorArgs([$this->factoryMock, 'key1', 'value1'])
                                ->getMockForAbstractClass();

        $parameterReflection = new ReflectionClass(AbstractParameter::class);
        $factoryProperty = $parameterReflection->getProperty('factory');
        $factoryProperty->setAccessible(true);

        $this->assertSame('key1', $this->parameter->getName());
        $this->assertSame('value1', $this->parameter->getValue());
        $this->assertSame(PDO::PARAM_STR, $this->parameter->getType());
        $this->assertSame($this->factoryMock, $factoryProperty->getValue($this->parameter));
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
                  ->method('bindValue')
                  ->with('key1', 'value1', PDO::PARAM_STR);

        $this->parameter->bindParam($statement);
    }
}