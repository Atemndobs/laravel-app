<?php

namespace Tests\Feature\Console\Commands\Recommendation\Catalog;

use App\Console\Commands\Recommendation\Catalog\createCatalogCommand;
use Tests\TestCase;

/**
 * Class createCatalogCommandTest.
 *
 * @covers \App\Console\Commands\Recommendation\Catalog\createCatalogCommand
 */
final class createCatalogCommandTest extends TestCase
{
    private createCatalogCommand $createCatalogCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->createCatalogCommand = new createCatalogCommand();
        $this->app->instance(createCatalogCommand::class, $this->createCatalogCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->createCatalogCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('catalog:create {id?} ')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
