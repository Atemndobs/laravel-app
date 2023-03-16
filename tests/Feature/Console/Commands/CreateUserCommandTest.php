<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\CreateUserCommand;
use Tests\TestCase;

/**
 * Class CreateUserCommandTest.
 *
 * @covers \App\Console\Commands\CreateUserCommand
 */
final class CreateUserCommandTest extends TestCase
{
    private CreateUserCommand $createUserCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->createUserCommand = new CreateUserCommand();
        $this->app->instance(CreateUserCommand::class, $this->createUserCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->createUserCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
