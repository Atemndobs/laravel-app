<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SpotifyCleanupcommand;
use Tests\TestCase;

/**
 * Class SpotifyCleanupcommandTest.
 *
 * @covers \App\Console\Commands\Song\SpotifyCleanupcommand
 */
final class SpotifyCleanupcommandTest extends TestCase
{
    private SpotifyCleanupcommand $spotifyCleanupcommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->spotifyCleanupcommand = new SpotifyCleanupcommand();
        $this->app->instance(SpotifyCleanupcommand::class, $this->spotifyCleanupcommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifyCleanupcommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:spotify-cleanupcommand')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
