<?php

namespace Tests\Unit\Listeners;

use App\Listeners\DownloadSpotifyListener;
use Tests\TestCase;

/**
 * Class DownloadSpotifyListenerTest.
 *
 * @covers \App\Listeners\DownloadSpotifyListener
 */
final class DownloadSpotifyListenerTest extends TestCase
{
    private DownloadSpotifyListener $downloadSpotifyListener;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->downloadSpotifyListener = new DownloadSpotifyListener();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->downloadSpotifyListener);
    }

    public function testHandle(): void
    {
        $event = new \stdClass();

        /** @todo This test is incomplete. */
        $this->downloadSpotifyListener->handle($event);
    }
}
