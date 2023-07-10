<?php

namespace Tests\Unit\Events;

use App\Events\SpotifySongEvent;
use Tests\TestCase;

/**
 * Class SpotifySongEventTest.
 *
 * @covers \App\Events\SpotifySongEvent
 */
final class SpotifySongEventTest extends TestCase
{
    private SpotifySongEvent $spotifySongEvent;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->spotifySongEvent = new SpotifySongEvent();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifySongEvent);
    }

    public function testBroadcastOn(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
