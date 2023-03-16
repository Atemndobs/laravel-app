<?php

namespace Tests\Feature\Console\Commands\Nest;

use App\Console\Commands\Nest\RestartNestCommand;
use Tests\TestCase;

/**
 * Class RestartNestCommandTest.
 *
 * @covers \App\Console\Commands\Nest\RestartNestCommand
 */
final class RestartNestCommandTest extends TestCase
{
    private RestartNestCommand $restartNestCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->restartNestCommand = new RestartNestCommand();
        $this->app->instance(RestartNestCommand::class, $this->restartNestCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->restartNestCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('nest:restart')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
