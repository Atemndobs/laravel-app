<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\CollectGenres;
use Tests\TestCase;

/**
 * Class CollectGenresTest.
 *
 * @covers \App\Console\Commands\Song\CollectGenres
 */
final class CollectGenresTest extends TestCase
{
    private CollectGenres $collectGenres;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->collectGenres = new CollectGenres();
        $this->app->instance(CollectGenres::class, $this->collectGenres);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->collectGenres);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:collect-genres')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
