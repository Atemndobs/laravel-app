<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SongUpdateCommand;
use Tests\TestCase;

/**
 * Class SongUpdateCommandTest.
 *
 * @covers \App\Console\Commands\Song\SongUpdateCommand
 */
final class SongUpdateCommandTest extends TestCase
{
    private SongUpdateCommand $songUpdateCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songUpdateCommand = new SongUpdateCommand();
        $this->app->instance(SongUpdateCommand::class, $this->songUpdateCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songUpdateCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:update {id?}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testUpdateSong(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
