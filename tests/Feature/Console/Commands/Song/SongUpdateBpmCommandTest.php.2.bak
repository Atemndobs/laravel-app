<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SongUpdateBpmCommand;
use Tests\TestCase;

/**
 * Class SongUpdateBpmCommandTest.
 *
 * @covers \App\Console\Commands\Song\SongUpdateBpmCommand
 */
final class SongUpdateBpmCommandTest extends TestCase
{
    private SongUpdateBpmCommand $songUpdateBpmCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songUpdateBpmCommand = new SongUpdateBpmCommand();
        $this->app->instance(SongUpdateBpmCommand::class, $this->songUpdateBpmCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songUpdateBpmCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:bpm {slug?} {--f|field=null}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testGetUpdatedSong(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
