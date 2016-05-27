<?php
use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\parameter\ArrayParameter;
use database\QueryBundle\parameter\IntegerParameter;
use database\QueryBundle\parameter\StringParameter;
use database\QueryBundle\query\Query;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 08:38
 */
class QueryTest extends PHPUnit_Framework_TestCase {
    /**
     * @var QueryFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $factoryMock;
    /**
     * @var ConnectionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    /**
     * @var ReflectionProperty
     */
    private $parameterProperty;

    /**
     * @var ReflectionProperty
     */
    private $statementProperty;

    protected function setUp () {
        $this->factoryMock = $this->getMockBuilder(QueryFactory::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->connection = $this->getMockBuilder(ConnectionInterface::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->factoryMock->expects($this->any())
                          ->method('getConnection')
                          ->will($this->returnValue($this->connection));

        $queryReflection = new ReflectionClass(Query::class);
        $this->parameterProperty = $queryReflection->getProperty('parameters');
        $this->parameterProperty->setAccessible(true);
        $this->statementProperty = $queryReflection->getProperty('_statement');
        $this->statementProperty->setAccessible(true);
    }

    /**
     * @test
     */
    public function construct () {
        $queryReflection = new ReflectionClass(Query::class);
        $factoryProperty = $queryReflection->getProperty('factory');
        $parameterProperty = $queryReflection->getProperty('parameters');
        $statementProperty = $queryReflection->getProperty('_statement');
        $currentSqlProperty = $queryReflection->getProperty('currentSql');
        $factoryProperty->setAccessible(true);
        $parameterProperty->setAccessible(true);
        $statementProperty->setAccessible(true);
        $currentSqlProperty->setAccessible(true);

        $query = new Query($this->factoryMock, 'SELECT * FROM test');

        $this->assertSame($this->factoryMock, $factoryProperty->getValue($query));
        $this->assertSame('SELECT * FROM test', $query->getSql());
        $this->assertSame([], $parameterProperty->getValue($query));
        $this->assertSame(null, $statementProperty->getValue($query));
        $this->assertSame('', $currentSqlProperty->getValue($query));
    }

    /**
     * @test
     */
    public function setFetchMode () {
        $query = new Query($this->factoryMock, 'SELECT * FROM test');
        $statementMock = $this->getMockBuilder(StatementInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->statementProperty->setValue($query, $statementMock);
        $statementMock->expects($this->once())
                      ->method('setFetchMode')
                      ->with('test')
                      ->will($this->returnValue('success'));
        $this->assertSame('success', $query->setFetchMode('test'));
    }

    /**
     * @test
     */
    public function buildResult () {
        $queryReflection = new ReflectionClass(Query::class);
        $parameterProperty = $queryReflection->getProperty('parameters');
        $currentSqlProperty = $queryReflection->getProperty('currentSql');
        $parameterProperty->setAccessible(true);
        $currentSqlProperty->setAccessible(true);

        $param1Mock = $this->getMockBuilder(StringParameter::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $param2Mock = $this->getMockBuilder(ArrayParameter::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $statementMock = $this->getMockBuilder(StatementInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $query = new Query($this->factoryMock,
                           'SELECT * FROM test WHERE param1 = :param1 AND param2 IN(:param2)');

        $parameterProperty->setValue($query, ['param1' => $param1Mock, 'param2' => $param2Mock]);

        $param1Mock->expects($this->any())
                   ->method('prepare')
                   ->with('SELECT * FROM test WHERE param1 = :param1 AND param2 IN(:param2)')
                   ->will($this->returnValue('SELECT * FROM test WHERE param1 = :param1 AND param2 IN(:param2)'));

        $param1Mock->expects($this->at(1))
                   ->method('bindParam')
                   ->with($statementMock);

        $param2Mock->expects($this->any())
                   ->method('prepare')
                   ->with('SELECT * FROM test WHERE param1 = :param1 AND param2 IN(:param2)')
                   ->will($this->returnValue('SELECT * FROM test WHERE param1 = :param1 AND param2 IN(:param2_0,:param2_1)'));
        $param2Mock->expects($this->at(1))
                   ->method('bindParam')
                   ->with($statementMock);

        $this->connection->expects($this->once())
                         ->method('prepare')
                         ->with('SELECT * FROM test WHERE param1 = :param1 AND param2 IN(:param2_0,:param2_1)')
                         ->will($this->returnValue($statementMock));

        $statementMock->expects($this->at(0))
                      ->method('execute');


        $this->assertSame($statementMock, $query->buildResult());

        $this->assertSame('SELECT * FROM test WHERE param1 = :param1 AND param2 IN(:param2_0,:param2_1)',
                          $currentSqlProperty->getValue($query));

        $this->assertSame($statementMock, $query->buildResult());

        $this->assertSame('SELECT * FROM test WHERE param1 = :param1 AND param2 IN(:param2_0,:param2_1)',
                          $currentSqlProperty->getValue($query));
    }

    /**
     * @test
     */
    public function setParameter () {
        $query = new Query($this->factoryMock, 'SELECT * FROM test');

        $key1ParameterMock = $this->getMockBuilder(StringParameter::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->factoryMock->expects($this->at(0))
                          ->method('createParameter')
                          ->with('key1', 'value1', null)
                          ->will($this->returnValue($key1ParameterMock));

        $this->assertSame($query, $query->setParameter('key1', 'value1'));
        $this->assertSame(['key1' => $key1ParameterMock], $this->parameterProperty->getValue($query));

        return $query;
    }

    /**
     * @test
     */
    public function setParameters () {
        $query = new Query($this->factoryMock, 'SELECT * FROM test');

        $key1ParameterMock = $this->getMockBuilder(StringParameter::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $key2ParameterMock = $this->getMockBuilder(StringParameter::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->factoryMock->expects($this->at(0))
                          ->method('createParameter')
                          ->with('key1', 'value1', null)
                          ->will($this->returnValue($key1ParameterMock));
        $this->factoryMock->expects($this->at(1))
                          ->method('createParameter')
                          ->with('key2', 'value2', null)
                          ->will($this->returnValue($key2ParameterMock));
        $this->assertSame($query, $query->setParameters(['key1' => 'value1', 'key2' => 'value2']));
        $this->assertSame(['key1' => $key1ParameterMock, 'key2' => $key2ParameterMock],
                          $this->parameterProperty->getValue($query));
    }

    /**
     * @test
     */
    public function resetParameter_withAnotherType () {
        $query = $this->setParameter();

        $key1Parameter2Mock = $this->getMockBuilder(IntegerParameter::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $key1Parameter2Mock->expects($this->at(0))
                           ->method('setValue')
                           ->with(3);

        $this->factoryMock->expects($this->at(0))
                          ->method('createParameter')
                          ->with('key1', 3, IntegerParameter::class)
                          ->will($this->returnValue($key1Parameter2Mock));

        $this->assertSame($query, $query->setParameter('key1', 3, IntegerParameter::class));
        $this->assertSame(['key1' => $key1Parameter2Mock], $this->parameterProperty->getValue($query));
    }

    /**
     * @test
     */
    public function resetParameter_withSameType () {
        $query = $this->setParameter();

        $this->factoryMock->expects($this->never())
                          ->method('createParameter');

        $this->assertSame($query, $query->setParameter('key1', 3, StringParameter::class));
    }

    /**
     * @test
     */
    public function resetParameter_withoutType () {
        $query = $this->setParameter();

        $this->factoryMock->expects($this->never())
                          ->method('createParameter');

        $this->assertSame($query, $query->setParameter('key1', 'value2'));
    }
}