<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Analysis\CheckAnalyzedSongsCommand;
use Tests\TestCase;

/**
 * Class CheckAnalyzedSongsCommandTest.
 *
 * @covers \App\Console\Commands\Analysis\CheckAnalyzedSongsCommand
 */
final class CheckAnalyzedSongsCommandTest extends TestCase
{
    private CheckAnalyzedSongsCommand $checkAnalyzedSongsCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->checkAnalyzedSongsCommand = new CheckAnalyzedSongsCommand();
        $this->app->instance(CheckAnalyzedSongsCommand::class, $this->checkAnalyzedSongsCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->checkAnalyzedSongsCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
