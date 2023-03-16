<?php

namespace Tests\Feature\Console\Commands\Db;

use App\Console\Commands\Db\DbBackupCommand;
use Tests\TestCase;

/**
 * Class DbBackupCommandTest.
 *
 * @covers \App\Console\Commands\Db\DbBackupCommand
 */
final class DbBackupCommandTest extends TestCase
{
    private DbBackupCommand $dbBackupCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->dbBackupCommand = new DbBackupCommand();
        $this->app->instance(DbBackupCommand::class, $this->dbBackupCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->dbBackupCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('db:bk')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
