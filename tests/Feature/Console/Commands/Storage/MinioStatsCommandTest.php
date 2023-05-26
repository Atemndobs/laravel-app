<?php

namespace Tests\Feature\Console\Commands\Storage;

use App\Console\Commands\Storage\MinioStatsCommand;
use Tests\TestCase;

/**
 * Class MinioStatsCommandTest.
 *
 * @covers \App\Console\Commands\Storage\MinioStatsCommand
 */
final class MinioStatsCommandTest extends TestCase
{
    private MinioStatsCommand $minioStatsCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->minioStatsCommand = new MinioStatsCommand();
        $this->app->instance(MinioStatsCommand::class, $this->minioStatsCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->minioStatsCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:minio-stats-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
