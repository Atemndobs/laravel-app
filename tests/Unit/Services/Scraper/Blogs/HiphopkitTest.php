<?php

namespace Tests\Unit\Services\Scraper\Blogs;

use App\Services\Scraper\Blogs\Hiphopkit;
use Tests\TestCase;

/**
 * Class HiphopkitTest.
 *
 * @covers \App\Services\Scraper\Blogs\Hiphopkit
 */
final class HiphopkitTest extends TestCase
{
    private Hiphopkit $hiphopkit;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->hiphopkit = new Hiphopkit();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->hiphopkit);
    }

    public function testGetSongsFromHiphopkit(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
