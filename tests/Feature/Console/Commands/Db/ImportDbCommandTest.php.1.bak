<?php

namespace Tests\Feature\Console\Commands\Db;

use App\Console\Commands\Db\ImportDbCommand;
use Tests\TestCase;

/**
 * Class ImportDbCommandTest.
 *
 * @covers \App\Console\Commands\Db\ImportDbCommand
 */
final class ImportDbCommandTest extends TestCase
{
    private ImportDbCommand $importDbCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->importDbCommand = new ImportDbCommand();
        $this->app->instance(ImportDbCommand::class, $this->importDbCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->importDbCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('import:db')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
