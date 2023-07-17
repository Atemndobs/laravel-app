<?php

namespace Tests\Feature\Console\Commands\Spotify;

use App\Console\Commands\Spotify\ReleaseRadar;
use Tests\TestCase;

/**
 * Class ReleaseRadarTest.
 *
 * @covers \App\Console\Commands\Spotify\ReleaseRadar
 */
final class ReleaseRadarTest extends TestCase
{
    private ReleaseRadar $releaseRadar;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->releaseRadar = new ReleaseRadar();
        $this->app->instance(ReleaseRadar::class, $this->releaseRadar);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->releaseRadar);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:release-radar')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
