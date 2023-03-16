<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\CatalogCrudController;
use Tests\TestCase;

/**
 * Class CatalogCrudControllerTest.
 *
 * @covers \App\Http\Controllers\Admin\CatalogCrudController
 */
final class CatalogCrudControllerTest extends TestCase
{
    private CatalogCrudController $catalogCrudController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->catalogCrudController = new CatalogCrudController();
        $this->app->instance(CatalogCrudController::class, $this->catalogCrudController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->catalogCrudController);
    }

    public function testSetup(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
