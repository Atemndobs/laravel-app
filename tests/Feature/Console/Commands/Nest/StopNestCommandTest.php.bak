<?php

namespace Tests\Feature\Console\Commands\Nest;

use App\Console\Commands\Nest\StopNestCommand;
use Tests\TestCase;

/**
 * Class StopNestCommandTest.
 *
 * @covers \App\Console\Commands\Nest\StopNestCommand
 */
final class StopNestCommandTest extends TestCase
{
    private StopNestCommand $stopNestCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->stopNestCommand = new StopNestCommand();
        $this->app->instance(StopNestCommand::class, $this->stopNestCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->stopNestCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('nest:stop')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
