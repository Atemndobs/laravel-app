<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\PhpScraperCommand;
use Tests\TestCase;

/**
 * Class PhpScraperCommandTest.
 *
 * @covers \App\Console\Commands\PhpScraperCommand
 */
final class PhpScraperCommandTest extends TestCase
{
    private PhpScraperCommand $phpScraperCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->phpScraperCommand = new PhpScraperCommand();
        $this->app->instance(PhpScraperCommand::class, $this->phpScraperCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->phpScraperCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
