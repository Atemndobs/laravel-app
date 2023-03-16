<?php

namespace Tests\Feature\Console\Commands\Db;

use App\Console\Commands\Db\ImportUserCommand;
use Tests\TestCase;

/**
 * Class ImportUserCommandTest.
 *
 * @covers \App\Console\Commands\Db\ImportUserCommand
 */
final class ImportUserCommandTest extends TestCase
{
    private ImportUserCommand $importUserCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->importUserCommand = new ImportUserCommand();
        $this->app->instance(ImportUserCommand::class, $this->importUserCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->importUserCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('import:user {currentPage?} {pageSize?} {--p|page=0} {--s|size=20} {--f|field=null} {--l|limit=10}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testGetData(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetImportedDataPerPage(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
