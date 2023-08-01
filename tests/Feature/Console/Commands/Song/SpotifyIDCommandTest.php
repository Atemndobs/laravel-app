<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SpotifyIDCommand;
use Tests\TestCase;

/**
 * Class SpotifyIDCommandTest.
 *
 * @covers \App\Console\Commands\Song\SpotifyIDCommand
 */
final class SpotifyIDCommandTest extends TestCase
{
    private SpotifyIDCommand $spotifyIDCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->spotifyIDCommand = new SpotifyIDCommand();
        $this->app->instance(SpotifyIDCommand::class, $this->spotifyIDCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifyIDCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:spotify-i-d-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
