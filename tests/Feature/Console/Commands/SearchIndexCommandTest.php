<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\SearchIndexCommand;
use Tests\TestCase;

/**
 * Class SearchIndexCommandTest.
 *
 * @covers \App\Console\Commands\SearchIndexCommand
 */
final class SearchIndexCommandTest extends TestCase
{
    private SearchIndexCommand $searchIndexCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->searchIndexCommand = new SearchIndexCommand();
        $this->app->instance(SearchIndexCommand::class, $this->searchIndexCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->searchIndexCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
