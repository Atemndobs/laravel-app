<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SongUpdateBpmJob;
use Tests\TestCase;

/**
 * Class SongUpdateBpmJobTest.
 *
 * @covers \App\Jobs\SongUpdateBpmJob
 */
final class SongUpdateBpmJobTest extends TestCase
{
    private SongUpdateBpmJob $songUpdateBpmJob;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->songUpdateBpmJob = new SongUpdateBpmJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songUpdateBpmJob);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->songUpdateBpmJob->handle();
    }
}
