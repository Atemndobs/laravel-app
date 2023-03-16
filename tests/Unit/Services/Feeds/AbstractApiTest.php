<?php

namespace Tests\Unit\Services\Feeds;

use App\Services\Feeds\AbstractApi;
use Tests\TestCase;

/**
 * Class AbstractApiTest.
 *
 * @covers \App\Services\Feeds\AbstractApi
 */
final class AbstractApiTest extends TestCase
{
    private AbstractApi $abstractApi;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->abstractApi = new AbstractApi();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->abstractApi);
    }

    public function testAbstractApi(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testTest(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
