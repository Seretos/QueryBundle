<?php
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\interfaces\ParameterInterface;
use database\QueryBundle\parameter\ArrayParameter;
use database\QueryBundle\parameter\StringParameter;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 06:55
 */
class ArrayParameterTest extends PHPUnit_Framework_TestCase {
    /**
     * @var ArrayParameter
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

        $this->parameter = new ArrayParameter($this->factoryMock, 'key1', []);

        $this->assertSame('key1', $this->parameter->getName());
        $this->assertSame([], $this->parameter->getValue());
    }

    /**
     * @test
     */
    public function setValue () {
        $arrayParam1Mock = $this->getMockBuilder(ParameterInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $arrayParam2Mock = $this->getMockBuilder(ParameterInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->factoryMock->expects($this->exactly(2))
                          ->method('createParameter')
                          ->will($this->returnValueMap([['key1_0', 1, null, $arrayParam1Mock],
                                                        ['key1_1', 2, StringParameter::class, $arrayParam2Mock]]));

        $this->parameter->setValue([1, ['value' => 2, 'type' => StringParameter::class]]);
        $this->assertSame([$arrayParam1Mock, $arrayParam2Mock], $this->parameter->getValue());
    }

    private function createArrayParamMock ($name) {
        $arrayParamMock = $this->getMockBuilder(ParameterInterface::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        $arrayParamMock->expects($this->any())
                       ->method('getName')
                       ->will($this->returnValue($name));

        return $arrayParamMock;
    }

    /**
     * @test
     */
    public function prepare () {
        $arrayParam1Mock = $this->createArrayParamMock('key1_0');
        $arrayParam2Mock = $this->createArrayParamMock('key1_1');
        $arrayParam3Mock = $this->createArrayParamMock('key1_2');

        $this->factoryMock->expects($this->exactly(3))
                          ->method('createParameter')
                          ->will($this->returnValueMap([['key1_0', 1, null, $arrayParam1Mock],
                                                        ['key1_1', 2, null, $arrayParam2Mock],
                                                        ['key1_2', 3, null, $arrayParam3Mock]]));

        $this->parameter->setValue([1, 2, 3]);
        $this->assertSame('SELECT * FROM test WHERE id IN(:key1_0,:key1_1,:key1_2)',
                          $this->parameter->prepare('SELECT * FROM test WHERE id IN(:key1)'));
    }

    /**
     * @test
     */
    public function bindParam () {
        $arrayParam1Mock = $this->createArrayParamMock('key1_0');
        $arrayParam2Mock = $this->createArrayParamMock('key1_1');
        $arrayParam3Mock = $this->createArrayParamMock('key1_2');

        $this->factoryMock->expects($this->exactly(3))
                          ->method('createParameter')
                          ->will($this->returnValueMap([['key1_0', 1, null, $arrayParam1Mock],
                                                        ['key1_1', 2, null, $arrayParam2Mock],
                                                        ['key1_2', 3, null, $arrayParam3Mock]]));

        $this->parameter->setValue([1, 2, 3]);

        /* @var $statementMock PHPUnit_Framework_MockObject_MockObject|StatementInterface */
        $statementMock = $this->getMockBuilder(StatementInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $arrayParam1Mock->expects($this->once())
                        ->method('bindParam')
                        ->with($statementMock);

        $arrayParam2Mock->expects($this->once())
                        ->method('bindParam')
                        ->with($statementMock);

        $arrayParam3Mock->expects($this->once())
                        ->method('bindParam')
                        ->with($statementMock);

        $this->assertSame(true, $this->parameter->bindParam($statementMock));
    }
}