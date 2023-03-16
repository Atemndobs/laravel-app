<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Server\ServerMonitorCommand;
use Tests\TestCase;

/**
 * Class ServerMonitorCommandTest.
 *
 * @covers \App\Console\Commands\Server\ServerMonitorCommand
 */
final class ServerMonitorCommandTest extends TestCase
{
    private ServerMonitorCommand $serverMonitorCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->serverMonitorCommand = new ServerMonitorCommand();
        $this->app->instance(ServerMonitorCommand::class, $this->serverMonitorCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->serverMonitorCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
