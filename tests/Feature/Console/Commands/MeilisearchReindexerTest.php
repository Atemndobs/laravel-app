<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Indexer\MeilisearchReindexer;
use Tests\TestCase;

/**
 * Class MeilisearchReindexerTest.
 *
 * @covers \App\Console\Commands\Indexer\MeilisearchReindexer
 */
final class MeilisearchReindexerTest extends TestCase
{
    private MeilisearchReindexer $meilisearchReindexer;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->meilisearchReindexer = new MeilisearchReindexer();
        $this->app->instance(MeilisearchReindexer::class, $this->meilisearchReindexer);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->meilisearchReindexer);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
