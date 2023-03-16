<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\SongCrudController;
use Tests\TestCase;

/**
 * Class SongCrudControllerTest.
 *
 * @covers \App\Http\Controllers\Admin\SongCrudController
 */
final class SongCrudControllerTest extends TestCase
{
    private SongCrudController $songCrudController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->songCrudController = new SongCrudController();
        $this->app->instance(SongCrudController::class, $this->songCrudController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songCrudController);
    }

    public function testSetup(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
