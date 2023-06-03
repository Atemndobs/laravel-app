<?php

namespace Tests\Feature\Console\Commands\Storage;

use App\Console\Commands\Storage\DatabaseBackupStorageCommand;
use Tests\TestCase;

/**
 * Class DatabaseBackupStorageCommandTest.
 *
 * @covers \App\Console\Commands\Storage\DatabaseBackupStorageCommand
 */
final class DatabaseBackupStorageCommandTest extends TestCase
{
    private DatabaseBackupStorageCommand $databaseBackupStorageCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->databaseBackupStorageCommand = new DatabaseBackupStorageCommand();
        $this->app->instance(DatabaseBackupStorageCommand::class, $this->databaseBackupStorageCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->databaseBackupStorageCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:database-backup-storage-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
