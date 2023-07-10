<?php

namespace Tests\Feature\Console\Commands\Storage;

use App\Console\Commands\Storage\MinioGetUrlCommand;
use Tests\TestCase;

/**
 * Class MinioGetUrlCommandTest.
 *
 * @covers \App\Console\Commands\Storage\MinioGetUrlCommand
 */
final class MinioGetUrlCommandTest extends TestCase
{
    private MinioGetUrlCommand $minioGetUrlCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->minioGetUrlCommand = new MinioGetUrlCommand();
        $this->app->instance(MinioGetUrlCommand::class, $this->minioGetUrlCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->minioGetUrlCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:minio-get-url-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
