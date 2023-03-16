<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\FileCrudController;
use Tests\TestCase;

/**
 * Class FileCrudControllerTest.
 *
 * @covers \App\Http\Controllers\Admin\FileCrudController
 */
final class FileCrudControllerTest extends TestCase
{
    private FileCrudController $fileCrudController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->fileCrudController = new FileCrudController();
        $this->app->instance(FileCrudController::class, $this->fileCrudController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->fileCrudController);
    }

    public function testSetup(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
