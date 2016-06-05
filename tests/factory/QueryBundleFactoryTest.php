<?php
use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\QueryBundle\factory\QueryBundleFactory;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\query\Query;

/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 05.06.16
 * Time: 01:02
 */
class QueryBundleFactoryTest extends PHPUnit_Framework_TestCase {
    /**
     * @var ConnectionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConnection;

    /**
     * @var QueryFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockQueryFactory;
    /**
     * @var QueryBundleFactory
     */
    private $factory;

    protected function setUp () {
        $reflection = new ReflectionClass(QueryBundleFactory::class);
        $factoryProperty = $reflection->getProperty('queryFactory');
        $factoryProperty->setAccessible(true);

        $this->mockConnection = $this->getMockBuilder(ConnectionInterface::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->mockQueryFactory = $this->getMockBuilder(QueryFactory::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->factory = new QueryBundleFactory($this->mockConnection);

        $this->assertInstanceOf(QueryFactory::class, $factoryProperty->getValue($this->factory));
        $factoryProperty->setValue($this->factory, $this->mockQueryFactory);
    }

    /**
     * @test
     */
    public function registerTypeCondition () {
        $this->mockQueryFactory->expects($this->once())
                               ->method('registerTypeCondition')
                               ->with('test');
        $this->factory->registerTypeCondition('test');
    }

    /**
     * @test
     */
    public function createQuery () {
        $result = $this->factory->createQuery('test');
        $this->assertInstanceOf(Query::class, $result);
    }
}