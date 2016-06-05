QueryBundle
===========
this bundle extends the DriverBundle with new parameter features for prepared statements

Installation
============
add the bundle in your composer.json as bellow:
```js
"require": {
    ...
    ,"Seretos/database/QueryBundle" : "v0.1.*"
    ,"Seretos/database/DriverBundle" : "v0.1.*"
},
"repositories" : [
    ...
    ,{
        "type" : "git",
        "url" : "https://github.com/Seretos/QueryBundle"
     }
    ,{
        "type" : "git",
        "url" : "https://github.com/Seretos/DriverBundle"
    }
]
```
and execute the composer update command

Usage
=====
first create a query factory with your [driver bundle connection](https://github.com/Seretos/DriverBundle)
and create a query object for your statement
```php
$queryFactory = new QueryBundleFactory($this->connection);
$query = $queryFactory->createQuery('SELECT * FROM example1');

//export the query object to an StatementInterface object (see DriverBundle)
$executedStatement = $query->buildResult();
```

with this query object you can use plugin based parameter types for your query parameters.

array parameter
---------------
```php
$query = $queryFactory->createQuery('SELECT * FROM example1 WHERE id IN(:ids)')
$query->setParameter('ids',[1,2,3]);

$result = $query->buildResult(); // generate a new statement 'SELECT * FROM example1 WHERE id IN(:ids_0,:ids_1,:ids_2)' with setted parameters

$query->setParameter('ids',[1,2,3,4]);
$result = $query->buildResult(); // generate a new statement 'SELECT * FROM example1 WHERE id IN(:ids_0,:ids_1,:ids_2,:ids_3)' with setted parameters

$query->setParameter('ids',[3,4,3,5]);
$result = $query->buildResult(); //return the last statement again executed with the new parameters
```

datetime parameter
------------------
```php
$query = $queryFactory->createQuery('SELECT * FROM example1 WHERE datecolumn BETWEEN :date1 AND :date2');
$query->setParameter('date1',new Datetime());
$query->setParameter('date2',new Datetime());

$result = $query->buildResult(); //set the datetime parameter in a valid mysql datetime format
```

simple parameters
-----------------

* boolean
* integer
* null
* resource
* string

custom parameters
=================
in the first step you require an class which implements the [ParameterInterface](interfaces/ParameterInterface.php)
```php
class MyCustomParameter extends AbstractParameter {
    public function prepare ($sql){
        //here you can change the query (as example for array parameters)
        return $sql;
    }
    
    public function bindParam (StatementInterface $statement) {
        //here you can bind the param in your format
        return $statement->bindParam($this->getName(), $this->getValue(), $this->getType());
    }
}
```
to use this class you can set the third setParameter argument.
```php
$query->setParameter('test','value',MyCustomParameter::class);
```

you can also register a parameter with an condition.
```php
class MyCustomCondition implements ConditionInterface {

    public function condition ($value) {
        return is_bool($value);
    }

    public function getParameterType () {
        return MyCustomParameter::class;
    }
}
```
register your condition:
```php
$queryFactory->registerTypeCondition(MyCustomCondition::class);
```

Road map
========
the following features are not implemented but required for version 1.0
