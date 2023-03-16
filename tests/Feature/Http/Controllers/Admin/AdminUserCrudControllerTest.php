<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminUserCrudController;
use Tests\TestCase;

/**
 * Class AdminUserCrudControllerTest.
 *
 * @covers \App\Http\Controllers\Admin\AdminUserCrudController
 */
final class AdminUserCrudControllerTest extends TestCase
{
    private AdminUserCrudController $adminUserCrudController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->adminUserCrudController = new AdminUserCrudController();
        $this->app->instance(AdminUserCrudController::class, $this->adminUserCrudController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->adminUserCrudController);
    }

    public function testSetup(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
