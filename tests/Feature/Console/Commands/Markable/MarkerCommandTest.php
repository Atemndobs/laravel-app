<?php

namespace Tests\Feature\Console\Commands\Markable;

use App\Console\Commands\Markable\MarkerCommand;
use Tests\TestCase;

/**
 * Class MarkerCommandTest.
 *
 * @covers \App\Console\Commands\Markable\MarkerCommand
 */
final class MarkerCommandTest extends TestCase
{
    private MarkerCommand $markerCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->markerCommand = new MarkerCommand();
        $this->app->instance(MarkerCommand::class, $this->markerCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->markerCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
