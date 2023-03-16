<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\UpdateSongDurationCommand;
use Tests\TestCase;

/**
 * Class UpdateSongDurationCommandTest.
 *
 * @covers \App\Console\Commands\Song\UpdateSongDurationCommand
 */
final class UpdateSongDurationCommandTest extends TestCase
{
    private UpdateSongDurationCommand $updateSongDurationCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->updateSongDurationCommand = new UpdateSongDurationCommand();
        $this->app->instance(UpdateSongDurationCommand::class, $this->updateSongDurationCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->updateSongDurationCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:duration {slug?} {--f|field=null}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
