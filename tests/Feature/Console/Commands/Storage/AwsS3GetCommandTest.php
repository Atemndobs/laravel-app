<?php

namespace Tests\Feature\Console\Commands\Storage;

use App\Console\Commands\Storage\AwsS3StatsCommand;
use Tests\TestCase;

/**
 * Class AwsS3GetCommandTest.
 *
 * @covers \App\Console\Commands\Storage\AwsS3StatsCommand
 */
final class AwsS3GetCommandTest extends TestCase
{
    private AwsS3StatsCommand $awsS3GetCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->awsS3GetCommand = new AwsS3StatsCommand();
        $this->app->instance(AwsS3StatsCommand::class, $this->awsS3GetCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->awsS3GetCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:aws-s3-get-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
