<?php

namespace Tests\Unit\Events;

use App\Events\NewSongEvent;
use Tests\TestCase;

/**
 * Class NewSongEventTest.
 *
 * @covers \App\Events\NewSongEvent
 */
final class NewSongEventTest extends TestCase
{
    private NewSongEvent $newSongEvent;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->newSongEvent = new NewSongEvent();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->newSongEvent);
    }

    public function testBroadcastOn(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
