<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\ConfigCompareCommand;
use Tests\TestCase;

/**
 * Class ConfigCompareCommandTest.
 *
 * @covers \App\Console\Commands\ConfigCompareCommand
 */
final class ConfigCompareCommandTest extends TestCase
{
    private ConfigCompareCommand $configCompareCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->configCompareCommand = new ConfigCompareCommand();
        $this->app->instance(ConfigCompareCommand::class, $this->configCompareCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->configCompareCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
