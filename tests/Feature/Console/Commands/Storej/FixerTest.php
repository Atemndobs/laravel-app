<?php

namespace Tests\Feature\Console\Commands\Storej;

use App\Console\Commands\Storej\Fixer;
use Tests\TestCase;

/**
 * Class FixerTest.
 *
 * @covers \App\Console\Commands\Storej\Fixer
 */
final class FixerTest extends TestCase
{
    private Fixer $fixer;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->fixer = new Fixer();
        $this->app->instance(Fixer::class, $this->fixer);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->fixer);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
