<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\FileWatcher\UploadAudioWatcherCommand;
use Tests\TestCase;

/**
 * Class UploadAudioWatcherCommandTest.
 *
 * @covers \App\Console\Commands\FileWatcher\UploadAudioWatcherCommand
 */
final class UploadAudioWatcherCommandTest extends TestCase
{
    private UploadAudioWatcherCommand $uploadAudioWatcherCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->uploadAudioWatcherCommand = new UploadAudioWatcherCommand();
        $this->app->instance(UploadAudioWatcherCommand::class, $this->uploadAudioWatcherCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->uploadAudioWatcherCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
