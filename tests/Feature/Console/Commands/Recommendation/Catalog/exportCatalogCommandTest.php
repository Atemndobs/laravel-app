<?php

namespace Tests\Feature\Console\Commands\Recommendation\Catalog;

use App\Console\Commands\Recommendation\Catalog\exportCatalogCommand;
use Tests\TestCase;

/**
 * Class exportCatalogCommandTest.
 *
 * @covers \App\Console\Commands\Recommendation\Catalog\exportCatalogCommand
 */
final class exportCatalogCommandTest extends TestCase
{
    private exportCatalogCommand $exportCatalogCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->exportCatalogCommand = new exportCatalogCommand();
        $this->app->instance(exportCatalogCommand::class, $this->exportCatalogCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->exportCatalogCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('catalog:export {fileName?} ')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
