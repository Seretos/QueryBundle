<?php
use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\interfaces\ParameterInterface;
use database\QueryBundle\parameter\ArrayParameter;
use database\QueryBundle\parameter\BooleanParameter;
use database\QueryBundle\parameter\DatetimeParameter;
use database\QueryBundle\parameter\IntegerParameter;
use database\QueryBundle\parameter\NullParameter;
use database\QueryBundle\parameter\ResourceParameter;
use database\QueryBundle\parameter\StringParameter;
use database\QueryBundle\result\Result;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 07:37
 */
class QueryFactoryTest extends PHPUnit_Framework_TestCase {
    /**
     * @var ConnectionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;
    /**
     * @var QueryFactory
     */
    private $factory;

    protected function setUp () {
        $this->connectionMock = $this->getMockBuilder(ConnectionInterface::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();

        $this->factory = new QueryFactory($this->connectionMock);
        $this->assertSame($this->connectionMock, $this->factory->getConnection());
    }

    /**
     * @test
     */
    public function createParameter () {
        $parameter = $this->factory->createParameter('key1', null, IntegerParameter::class);
        $this->assertInstanceOf(IntegerParameter::class, $parameter);
        $this->assertSame('key1', $parameter->getName());
        $this->assertSame(null, $parameter->getValue());

        $parameter = $this->factory->createParameter('key1', null);
        $this->assertInstanceOf(NullParameter::class, $parameter);
        $this->assertSame('key1', $parameter->getName());
        $this->assertSame(null, $parameter->getValue());

        $parameter = $this->factory->createParameter('key1', 0);
        $this->assertInstanceOf(IntegerParameter::class, $parameter);
        $this->assertSame('key1', $parameter->getName());
        $this->assertSame(0, $parameter->getValue());

        $parameter = $this->factory->createParameter('key1', false);
        $this->assertInstanceOf(BooleanParameter::class, $parameter);
        $this->assertSame('key1', $parameter->getName());
        $this->assertSame(false, $parameter->getValue());

        $fp = fopen(__DIR__.'/../../phpunit.xml', 'rb');
        $parameter = $this->factory->createParameter('key1', $fp);
        $this->assertInstanceOf(ResourceParameter::class, $parameter);
        $this->assertSame('key1', $parameter->getName());
        $this->assertSame($fp, $parameter->getValue());

        $parameter = $this->factory->createParameter('key1', '');
        $this->assertInstanceOf(StringParameter::class, $parameter);
        $this->assertSame('key1', $parameter->getName());
        $this->assertSame('', $parameter->getValue());

        $parameter = $this->factory->createParameter('key1', [1, 2]);
        $this->assertInstanceOf(ArrayParameter::class, $parameter);
        $this->assertSame('key1', $parameter->getName());
        $index = 0;
        foreach ($parameter->getValue() as $value) {
            /* @var $value ParameterInterface */
            $this->assertSame('key1_'.$index, $value->getName());
            $this->assertSame($index + 1, $value->getValue());
            $index++;
        }

        $date = new DateTime();
        $parameter = $this->factory->createParameter('key1', $date);
        $this->assertInstanceOf(DatetimeParameter::class, $parameter);
        $this->assertSame('key1', $parameter->getName());
        $this->assertSame($date, $parameter->getValue());
    }
}