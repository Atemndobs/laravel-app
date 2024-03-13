<?php

namespace Tests\Feature\Console\Commands\DB;

use App\Console\Commands\Db\DirectusRevisionsCommand;
use Tests\TestCase;

/**
 * Class DirectusRevisionsCommandTest.
 *
 * @covers \App\Console\Commands\Db\DirectusRevisionsCommand
 */
final class DirectusRevisionsCommandTest extends TestCase
{
    private DirectusRevisionsCommand $directusRevisionsCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->directusRevisionsCommand = new DirectusRevisionsCommand();
        $this->app->instance(DirectusRevisionsCommand::class, $this->directusRevisionsCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->directusRevisionsCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:directus-revisions-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
