<?php

namespace Tests\Feature\Console\Commands\Analysis;

use App\Console\Commands\Analysis\ClearClassifierCommand;
use Tests\TestCase;

/**
 * Class ClearClassifierCommandTest.
 *
 * @covers \App\Console\Commands\Analysis\ClearClassifierCommand
 */
final class ClearClassifierCommandTest extends TestCase
{
    private ClearClassifierCommand $clearClassifierCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->clearClassifierCommand = new ClearClassifierCommand();
        $this->app->instance(ClearClassifierCommand::class, $this->clearClassifierCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->clearClassifierCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:clean {table?}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testCleanSongDb(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
