<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Http\Controllers\Admin\FeedCrudController;
use Tests\TestCase;

/**
 * Class FeedCrudControllerTest.
 *
 * @covers \App\Http\Controllers\Admin\FeedCrudController
 */
final class FeedCrudControllerTest extends TestCase
{
    private FeedCrudController $feedCrudController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->feedCrudController = new FeedCrudController();
        $this->app->instance(FeedCrudController::class, $this->feedCrudController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->feedCrudController);
    }

    public function testSetup(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
