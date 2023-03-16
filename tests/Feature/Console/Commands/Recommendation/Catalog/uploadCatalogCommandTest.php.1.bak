<?php

namespace Tests\Feature\Console\Commands\Recommendation\Catalog;

use App\Console\Commands\Recommendation\Catalog\uploadCatalogCommand;
use Tests\TestCase;

/**
 * Class uploadCatalogCommandTest.
 *
 * @covers \App\Console\Commands\Recommendation\Catalog\uploadCatalogCommand
 */
final class uploadCatalogCommandTest extends TestCase
{
    private uploadCatalogCommand $uploadCatalogCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->uploadCatalogCommand = new uploadCatalogCommand();
        $this->app->instance(uploadCatalogCommand::class, $this->uploadCatalogCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->uploadCatalogCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
