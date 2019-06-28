<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Database;

class DatabaseTest extends TestCase
{
    public function testConstructorSetsConfiguration(): void
    {
        $this->markTestIncomplete();
    }

    public function testDestructorDisconnectsFromDatabase(): void
    {
        $this->markTestIncomplete();
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
        $this->markTestIncomplete();
    }

    public function testExecuteReturnsNullWhenExecutingInvalidQuery(): void
    {
        $this->markTestIncomplete();
    }

    public function testExecuteReturnsNullWhenQueruingNotExistingTable(): void
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
