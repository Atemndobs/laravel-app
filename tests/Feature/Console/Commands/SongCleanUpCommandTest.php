<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\SongCleanUpCommand;
use Tests\TestCase;

/**
 * Class SongCleanUpCommandTest.
 *
 * @covers \App\Console\Commands\SongCleanUpCommand
 */
final class SongCleanUpCommandTest extends TestCase
{
    private SongCleanUpCommand $songCleanUpCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songCleanUpCommand = new SongCleanUpCommand();
        $this->app->instance(SongCleanUpCommand::class, $this->songCleanUpCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songCleanUpCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
