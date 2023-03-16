<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Db\SongCreatedByUpdateCommand;
use Tests\TestCase;

/**
 * Class SongCreatedByUpdateCommandTest.
 *
 * @covers \App\Console\Commands\Db\SongCreatedByUpdateCommand
 */
final class SongCreatedByUpdateCommandTest extends TestCase
{
    private SongCreatedByUpdateCommand $songCreatedByUpdateCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songCreatedByUpdateCommand = new SongCreatedByUpdateCommand();
        $this->app->instance(SongCreatedByUpdateCommand::class, $this->songCreatedByUpdateCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songCreatedByUpdateCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
