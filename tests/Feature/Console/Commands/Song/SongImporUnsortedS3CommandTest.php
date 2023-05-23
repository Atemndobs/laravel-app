<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SongImporUnsortedS3Command;
use Tests\TestCase;

/**
 * Class SongImporUnsortedS3CommandTest.
 *
 * @covers \App\Console\Commands\Song\SongImporUnsortedS3Command
 */
final class SongImporUnsortedS3CommandTest extends TestCase
{
    private SongImporUnsortedS3Command $songImporUnsortedS3Command;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songImporUnsortedS3Command = new SongImporUnsortedS3Command();
        $this->app->instance(SongImporUnsortedS3Command::class, $this->songImporUnsortedS3Command);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songImporUnsortedS3Command);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:song-impor-unsorted-s3-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
