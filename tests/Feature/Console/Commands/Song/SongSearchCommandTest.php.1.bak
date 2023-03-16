<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SongSearchCommand;
use Tests\TestCase;

/**
 * Class SongSearchCommandTest.
 *
 * @covers \App\Console\Commands\Song\SongSearchCommand
 */
final class SongSearchCommandTest extends TestCase
{
    private SongSearchCommand $songSearchCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songSearchCommand = new SongSearchCommand();
        $this->app->instance(SongSearchCommand::class, $this->songSearchCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songSearchCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:search {source?} {--s|site=null} {--a|artist=null} {--t|title=null}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
