<?php

namespace Tests\Feature\Console\Commands\Recommendation\Catalog;

use App\Console\Commands\Recommendation\Catalog\createUsageDataCommand;
use Tests\TestCase;

/**
 * Class createUsageDataCommandTest.
 *
 * @covers \App\Console\Commands\Recommendation\Catalog\createUsageDataCommand
 */
final class createUsageDataCommandTest extends TestCase
{
    private createUsageDataCommand $createUsageDataCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->createUsageDataCommand = new createUsageDataCommand();
        $this->app->instance(createUsageDataCommand::class, $this->createUsageDataCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->createUsageDataCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('catalog:user')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
