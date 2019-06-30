<?php

namespace Grain\Tests;

use Grain\Tests\GrainTestCase;
use Grain\Database;

class DatabaseTest extends GrainTestCase
{
    public function testConstructorSetsConfiguration(): void
    {
        $config = array("foo" => "bar");

        $db = new Database($config);

        $this->assertEquals($config, $this->invokePrivateMethod($db, "getConfig"));
    }

    public function testDestructorDisconnectsFromDatabase(): void
    {
        $db = $this->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->setMethods(array("disconnect"))
            ->getMock();

        $db->expects($this->once())
            ->method("disconnect");

        $db->__destruct();
    }

    public function testConnectReturnsExistingConnectionWhenExists(): void
    {
        $connection = new \stdClass();
        $connection->foo = "bar";

        $db = new Database(array());

        $dbReflection = new \ReflectionClass($db);
        $dbProperty = $dbReflection->getProperty("connection");
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($db, $connection);

        $this->assertSame($connection, $db->connect());
    }

    public function testConnectReturnsSelfWhenConnectsToDatabaseSuccessfully(): void
    {
        $this->markTestIncomplete();
    }

    public function testConnectReturnsNullWhenConnectionToDatabaseFails(): void
    {
        $this->markTestIncomplete();
    }

    public function testDisconnectDistructsConnection(): void
    {
        $connection = new \stdClass();
        $connection->foo = "bar";

        $db = new Database(array());

        $dbReflection = new \ReflectionClass($db);
        $dbProperty = $dbReflection->getProperty("connection");
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($db, $connection);

        $this->assertSame($connection, $dbProperty->getValue($db));

        $db->disconnect();

        $this->assertNull($dbProperty->getValue($db));
    }

    public function testExecuteReturnsNullWhenExecutingInvalidQuery(): void
    {
        $this->markTestIncomplete();
    }

    public function testExecuteReturnsNullWhenQueryingNotExistingTable(): void
    {
        $this->markTestIncomplete();
    }

    public function testExecuteReturnsNullWhenSingleResultIsRequestedAndNoResultExists(): void
    {
        $this->markTestIncomplete();
    }

    public function testExecuteReturnsResultWhenSingleResultIsRequestedAndResultExists(): void
    {
        $this->markTestIncomplete();
    }

    public function testExecuteReturnsEmptyArrayWhenMultipleResultsAreRequestedAndNoResultsExist(): void
    {
        $this->markTestIncomplete();
    }

    public function testExecuteReturnsResultsWhenMultipleResultsAreRequestedAndResultsExist(): void
    {
        $this->markTestIncomplete();
    }

    public function testFindReturnsEmptyArrayWhenNoResultsExist(): void
    {
        $this->markTestIncomplete();
    }

    public function testFindReturnsResultsWhenResultsExist(): void
    {
        $this->markTestIncomplete();
    }

    public function testFindOneReturnsNullWhenNoResultExists(): void
    {
        $this->markTestIncomplete();
    }

    public function testFindOneReturnsResultWhenResultExists(): void
    {
        $this->markTestIncomplete();
    }
}
