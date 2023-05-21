<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\MinioFileStorageCommand;
use Tests\TestCase;

/**
 * Class MinioFileStorageCommandTest.
 *
 * @covers \App\Console\Commands\MinioFileStorageCommand
 */
final class MinioFileStorageCommandTest extends TestCase
{
    private MinioFileStorageCommand $minioFileStorageCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->minioFileStorageCommand = new MinioFileStorageCommand();
        $this->app->instance(MinioFileStorageCommand::class, $this->minioFileStorageCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->minioFileStorageCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
