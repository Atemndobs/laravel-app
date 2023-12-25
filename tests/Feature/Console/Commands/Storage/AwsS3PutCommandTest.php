<?php

namespace Tests\Feature\Console\Commands\Storage;

use App\Console\Commands\Storage\AwsS3PutCommand;
use Tests\TestCase;

/**
 * Class AwsS3PutCommandTest.
 *
 * @covers \App\Console\Commands\Storage\AwsS3PutCommand
 */
final class AwsS3PutCommandTest extends TestCase
{
    private AwsS3PutCommand $awsS3PutCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->awsS3PutCommand = new AwsS3PutCommand();
        $this->app->instance(AwsS3PutCommand::class, $this->awsS3PutCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->awsS3PutCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:aws-s3-put-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
