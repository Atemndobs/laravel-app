<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SongSyncCommand;
use Tests\TestCase;

/**
 * Class SongSyncCommandTest.
 *
 * @covers \App\Console\Commands\Song\SongSyncCommand
 */
final class SongSyncCommandTest extends TestCase
{
    private SongSyncCommand $songSyncCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songSyncCommand = new SongSyncCommand();
        $this->app->instance(SongSyncCommand::class, $this->songSyncCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songSyncCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:song-sync-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
