<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\UsageCrudController;
use Tests\TestCase;

/**
 * Class UsageCrudControllerTest.
 *
 * @covers \App\Http\Controllers\Admin\UsageCrudController
 */
final class UsageCrudControllerTest extends TestCase
{
    private UsageCrudController $usageCrudController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->usageCrudController = new UsageCrudController();
        $this->app->instance(UsageCrudController::class, $this->usageCrudController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->usageCrudController);
    }

    public function testSetup(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
