<?php

namespace Tests\Feature\Console\Commands\Analysis;

use App\Console\Commands\Analysis\ReClassifyCommand;
use Tests\TestCase;

/**
 * Class ReClassifyCommandTest.
 *
 * @covers \App\Console\Commands\Analysis\ReClassifyCommand
 */
final class ReClassifyCommandTest extends TestCase
{
    private ReClassifyCommand $reClassifyCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->reClassifyCommand = new ReClassifyCommand();
        $this->app->instance(ReClassifyCommand::class, $this->reClassifyCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->reClassifyCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:reclassify {slug?} {--all}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testClassify(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
