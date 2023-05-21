<?php

namespace Tests\Unit\Jobs;

use App\Jobs\DownloadSpotifyJob;
use Tests\TestCase;

/**
 * Class DownloadSpotifyJobTest.
 *
 * @covers \App\Jobs\DownloadSpotifyJob
 */
final class DownloadSpotifyJobTest extends TestCase
{
    private DownloadSpotifyJob $downloadSpotifyJob;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->downloadSpotifyJob = new DownloadSpotifyJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->downloadSpotifyJob);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->downloadSpotifyJob->handle();
    }
}
