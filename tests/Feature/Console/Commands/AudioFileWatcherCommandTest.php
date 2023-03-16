<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\FileWatcher\AudioFileWatcherCommand;
use Tests\TestCase;

/**
 * Class AudioFileWatcherCommandTest.
 *
 * @covers \App\Console\Commands\FileWatcher\AudioFileWatcherCommand
 */
final class AudioFileWatcherCommandTest extends TestCase
{
    private AudioFileWatcherCommand $audioFileWatcherCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->audioFileWatcherCommand = new AudioFileWatcherCommand();
        $this->app->instance(AudioFileWatcherCommand::class, $this->audioFileWatcherCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->audioFileWatcherCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
