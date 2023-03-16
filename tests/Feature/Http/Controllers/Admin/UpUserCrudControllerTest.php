<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\UpUserCrudController;
use Tests\TestCase;

/**
 * Class UpUserCrudControllerTest.
 *
 * @covers \App\Http\Controllers\Admin\UpUserCrudController
 */
final class UpUserCrudControllerTest extends TestCase
{
    private UpUserCrudController $upUserCrudController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->upUserCrudController = new UpUserCrudController();
        $this->app->instance(UpUserCrudController::class, $this->upUserCrudController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->upUserCrudController);
    }

    public function testSetup(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
