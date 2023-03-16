<?php

namespace Tests\Feature\Console\Commands\Db;

use App\Console\Commands\Db\DbImportDumpCommand;
use Tests\TestCase;

/**
 * Class DbImportDumpCommandTest.
 *
 * @covers \App\Console\Commands\Db\DbImportDumpCommand
 */
final class DbImportDumpCommandTest extends TestCase
{
    private DbImportDumpCommand $dbImportDumpCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->dbImportDumpCommand = new DbImportDumpCommand();
        $this->app->instance(DbImportDumpCommand::class, $this->dbImportDumpCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->dbImportDumpCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('db:import')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testUnzipFile(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testDownloadFileFromBackupFolder(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
