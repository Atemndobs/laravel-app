<?php

namespace Tests\Feature\Console\Commands\Nest;

use App\Console\Commands\Nest\WatchNestCommand;
use Tests\TestCase;

/**
 * Class WatchNestCommandTest.
 *
 * @covers \App\Console\Commands\Nest\WatchNestCommand
 */
final class WatchNestCommandTest extends TestCase
{
    private WatchNestCommand $watchNestCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->watchNestCommand = new WatchNestCommand();
        $this->app->instance(WatchNestCommand::class, $this->watchNestCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->watchNestCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('nest:watch')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testHealthCheck(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testReStartNest(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
