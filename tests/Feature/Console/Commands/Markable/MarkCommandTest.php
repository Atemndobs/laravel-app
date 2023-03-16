<?php

namespace Tests\Feature\Console\Commands\Markable;

use App\Console\Commands\Markable\MarkCommand;
use Tests\TestCase;

/**
 * Class MarkCommandTest.
 *
 * @covers \App\Console\Commands\Markable\MarkCommand
 */
final class MarkCommandTest extends TestCase
{
    private MarkCommand $markCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->markCommand = new MarkCommand();
        $this->app->instance(MarkCommand::class, $this->markCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->markCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
