<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\StorjUploadCommand;
use Tests\TestCase;

/**
 * Class StorjUploadCommandTest.
 *
 * @covers \App\Console\Commands\StorjUploadCommand
 */
final class StorjUploadCommandTest extends TestCase
{
    private StorjUploadCommand $storjUploadCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->storjUploadCommand = new StorjUploadCommand();
        $this->app->instance(StorjUploadCommand::class, $this->storjUploadCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->storjUploadCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
