<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\FileWatcher\MoveAudioUploadsCommand;
use Tests\TestCase;

/**
 * Class MoveAudioUploadsCommandTest.
 *
 * @covers \App\Console\Commands\FileWatcher\MoveAudioUploadsCommand
 */
final class MoveAudioUploadsCommandTest extends TestCase
{
    private MoveAudioUploadsCommand $moveAudioUploadsCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->moveAudioUploadsCommand = new MoveAudioUploadsCommand();
        $this->app->instance(MoveAudioUploadsCommand::class, $this->moveAudioUploadsCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->moveAudioUploadsCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
