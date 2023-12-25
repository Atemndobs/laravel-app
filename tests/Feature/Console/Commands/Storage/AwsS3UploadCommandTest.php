<?php

namespace Tests\Feature\Console\Commands\Storage;

use App\Console\Commands\Storage\AwsS3UploadCommand;
use Tests\TestCase;

/**
 * Class AwsS3UploadCommandTest.
 *
 * @covers \App\Console\Commands\Storage\AwsS3UploadCommand
 */
final class AwsS3UploadCommandTest extends TestCase
{
    private AwsS3UploadCommand $awsS3UploadCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->awsS3UploadCommand = new AwsS3UploadCommand();
        $this->app->instance(AwsS3UploadCommand::class, $this->awsS3UploadCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->awsS3UploadCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:aws-s3-upload-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
