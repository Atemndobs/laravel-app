<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\OcrCommandContrller;
use Tests\TestCase;

/**
 * Class OcrCommandContrllerTest.
 *
 * @covers \App\Http\Controllers\OcrCommandContrller
 */
final class OcrCommandContrllerTest extends TestCase
{
    private OcrCommandContrller $ocrCommandContrller;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->ocrCommandContrller = new OcrCommandContrller();
        $this->app->instance(OcrCommandContrller::class, $this->ocrCommandContrller);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->ocrCommandContrller);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testStore(): void
    {
        /** @todo This test is incomplete. */
        $this->post('/path', [ /* data */ ])
            ->assertStatus(200);
    }

    public function testShow(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testUpdate(): void
    {
        /** @todo This test is incomplete. */
        $this->put('/path', [ /* data */ ])
            ->assertStatus(200);
    }

    public function testDestroy(): void
    {
        /** @todo This test is incomplete. */
        $this->delete('/path')
            ->assertStatus(200);
    }
}
