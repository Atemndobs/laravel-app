<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\SpotifySongDonwnloadCommand;
use Tests\TestCase;

/**
 * Class SpotifySongDonwnloadCommandTest.
 *
 * @covers \App\Console\Commands\SpotifySongDonwnloadCommand
 */
final class SpotifySongDonwnloadCommandTest extends TestCase
{
    private SpotifySongDonwnloadCommand $spotifySongDonwnloadCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->spotifySongDonwnloadCommand = new SpotifySongDonwnloadCommand();
        $this->app->instance(SpotifySongDonwnloadCommand::class, $this->spotifySongDonwnloadCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifySongDonwnloadCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
