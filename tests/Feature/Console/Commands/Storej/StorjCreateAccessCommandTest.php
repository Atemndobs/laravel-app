<?php

namespace Tests\Feature\Console\Commands\Storej;

use App\Console\Commands\Storej\StorjCreateAccessCommand;
use Tests\TestCase;

/**
 * Class StorjCreateAccessCommandTest.
 *
 * @covers \App\Console\Commands\Storej\StorjCreateAccessCommand
 */
final class StorjCreateAccessCommandTest extends TestCase
{
    private StorjCreateAccessCommand $storjCreateAccessCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->storjCreateAccessCommand = new StorjCreateAccessCommand();
        $this->app->instance(StorjCreateAccessCommand::class, $this->storjCreateAccessCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->storjCreateAccessCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
