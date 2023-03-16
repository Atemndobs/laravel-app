<?php

namespace Tests\Feature\Console\Commands\Nest;

use App\Console\Commands\Nest\StartNestCommand;
use Tests\TestCase;

/**
 * Class StartNestCommandTest.
 *
 * @covers \App\Console\Commands\Nest\StartNestCommand
 */
final class StartNestCommandTest extends TestCase
{
    private StartNestCommand $startNestCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->startNestCommand = new StartNestCommand();
        $this->app->instance(StartNestCommand::class, $this->startNestCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->startNestCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('nest:start')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
