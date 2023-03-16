<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\FindSongCommand;
use Tests\TestCase;

/**
 * Class FindSongCommandTest.
 *
 * @covers \App\Console\Commands\Song\FindSongCommand
 */
final class FindSongCommandTest extends TestCase
{
    private FindSongCommand $findSongCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->findSongCommand = new FindSongCommand();
        $this->app->instance(FindSongCommand::class, $this->findSongCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->findSongCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:find {--a|slug=null} {--b|bpm=null} {--s|scale=null} {--k|key=null}
    {--g|genre=null} {--t|title=null}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
