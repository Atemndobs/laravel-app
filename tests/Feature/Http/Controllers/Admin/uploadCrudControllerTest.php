<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\uploadCrudController;
use Tests\TestCase;

/**
 * Class uploadCrudControllerTest.
 *
 * @covers \App\Http\Controllers\Admin\uploadCrudController
 */
final class uploadCrudControllerTest extends TestCase
{
    private uploadCrudController $uploadCrudController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->uploadCrudController = new uploadCrudController();
        $this->app->instance(uploadCrudController::class, $this->uploadCrudController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->uploadCrudController);
    }

    public function testSetup(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
