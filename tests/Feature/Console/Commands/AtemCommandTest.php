<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\AtemCommand;
use Tests\TestCase;

/**
 * Class AtemCommandTest.
 *
 * @covers \App\Console\Commands\AtemCommand
 */
final class AtemCommandTest extends TestCase
{
    private AtemCommand $atemCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->atemCommand = new AtemCommand();
        $this->app->instance(AtemCommand::class, $this->atemCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->atemCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
