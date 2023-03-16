<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Scraper\SoundcloudDownloadCommand;
use Tests\TestCase;

/**
 * Class SoundcloudDownloadCommandTest.
 *
 * @covers \App\Console\Commands\Scraper\SoundcloudDownloadCommand
 */
final class SoundcloudDownloadCommandTest extends TestCase
{
    private SoundcloudDownloadCommand $soundcloudDownloadCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->soundcloudDownloadCommand = new SoundcloudDownloadCommand();
        $this->app->instance(SoundcloudDownloadCommand::class, $this->soundcloudDownloadCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->soundcloudDownloadCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
