<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Song\SongUpdateImage;
use Tests\TestCase;

/**
 * Class SongUpdateImageTest.
 *
 * @covers \App\Console\Commands\Song\SongUpdateImage
 */
final class SongUpdateImageTest extends TestCase
{
    private SongUpdateImage $songUpdateImage;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songUpdateImage = new SongUpdateImage();
        $this->app->instance(SongUpdateImage::class, $this->songUpdateImage);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songUpdateImage);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
