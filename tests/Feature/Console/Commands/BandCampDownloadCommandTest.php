<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Scraper\BandCampDownloadCommand;
use Tests\TestCase;

/**
 * Class BandCampDownloadCommandTest.
 *
 * @covers \App\Console\Commands\Scraper\BandCampDownloadCommand
 */
final class BandCampDownloadCommandTest extends TestCase
{
    private BandCampDownloadCommand $bandCampDownloadCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->bandCampDownloadCommand = new BandCampDownloadCommand();
        $this->app->instance(BandCampDownloadCommand::class, $this->bandCampDownloadCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->bandCampDownloadCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
