<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\image;
use Tests\TestCase;

/**
 * Class imageTest.
 *
 * @covers \App\Console\Commands\image
 */
final class imageTest extends TestCase
{
    private image $image;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->image = new image();
        $this->app->instance(image::class, $this->image);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->image);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
