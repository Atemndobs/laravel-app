<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SongUpdateGenreCommand;
use Tests\TestCase;

/**
 * Class SongUpdateGenreCommandTest.
 *
 * @covers \App\Console\Commands\Song\SongUpdateGenreCommand
 */
final class SongUpdateGenreCommandTest extends TestCase
{
    private SongUpdateGenreCommand $songUpdateGenreCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songUpdateGenreCommand = new SongUpdateGenreCommand();
        $this->app->instance(SongUpdateGenreCommand::class, $this->songUpdateGenreCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songUpdateGenreCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:genre {author?}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
