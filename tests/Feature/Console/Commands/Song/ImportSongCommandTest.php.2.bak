<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\ImportSongCommand;
use Tests\TestCase;

/**
 * Class ImportSongCommandTest.
 *
 * @covers \App\Console\Commands\Song\ImportSongCommand
 */
final class ImportSongCommandTest extends TestCase
{
    private ImportSongCommand $importSongCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->importSongCommand = new ImportSongCommand();
        $this->app->instance(ImportSongCommand::class, $this->importSongCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->importSongCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:import {source?}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testCleanDb(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testCleanFiles(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testDownloadStrapiSong(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
