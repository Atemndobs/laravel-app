<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\CleanUpAuthorCommand;
use Tests\TestCase;

/**
 * Class CleanUpAuthorCommandTest.
 *
 * @covers \App\Console\Commands\Song\CleanUpAuthorCommand
 */
final class CleanUpAuthorCommandTest extends TestCase
{
    private CleanUpAuthorCommand $cleanUpAuthorCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->cleanUpAuthorCommand = new CleanUpAuthorCommand();
        $this->app->instance(CleanUpAuthorCommand::class, $this->cleanUpAuthorCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->cleanUpAuthorCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:clean-up-author-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
