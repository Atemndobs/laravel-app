<?php

namespace Tests\Feature\Console\Commands\Analysis;

use App\Console\Commands\Analysis\AnalyzeSongCommand;
use Tests\TestCase;

/**
 * Class AnalyzeSongCommandTest.
 *
 * @covers \App\Console\Commands\Analysis\AnalyzeSongCommand
 */
final class AnalyzeSongCommandTest extends TestCase
{
    private AnalyzeSongCommand $analyzeSongCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->analyzeSongCommand = new AnalyzeSongCommand();
        $this->app->instance(AnalyzeSongCommand::class, $this->analyzeSongCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->analyzeSongCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:analyze {slug}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
