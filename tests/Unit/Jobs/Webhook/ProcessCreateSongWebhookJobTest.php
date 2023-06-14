<?php

namespace Tests\Unit\Jobs\Webhook;

use App\Jobs\Webhook\ProcessCreateSongWebhookJob;
use Tests\TestCase;

/**
 * Class ProcessCreateSongWebhookJobTest.
 *
 * @covers \App\Jobs\Webhook\ProcessCreateSongWebhookJob
 */
final class ProcessCreateSongWebhookJobTest extends TestCase
{
    private ProcessCreateSongWebhookJob $processCreateSongWebhookJob;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processCreateSongWebhookJob = new ProcessCreateSongWebhookJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->processCreateSongWebhookJob);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->processCreateSongWebhookJob->handle();
    }
}
