<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Scraper\BlogMusicDownloadCommand;
use Tests\TestCase;

/**
 * Class BlogMusicDownloadCommandTest.
 *
 * @covers \App\Console\Commands\Scraper\BlogMusicDownloadCommand
 */
final class BlogMusicDownloadCommandTest extends TestCase
{
    private BlogMusicDownloadCommand $blogMusicDownloadCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->blogMusicDownloadCommand = new BlogMusicDownloadCommand();
        $this->app->instance(BlogMusicDownloadCommand::class, $this->blogMusicDownloadCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->blogMusicDownloadCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
