<?php

namespace Tests\Feature\Console\Commands\Db;

use App\Console\Commands\Db\Tools;
use Tests\TestCase;

/**
 * Class ToolsTest.
 *
 * @covers \App\Console\Commands\Db\Tools
 */
final class ToolsTest extends TestCase
{
    private Tools $tools;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->tools = $this->getMockBuilder(Tools::class)
            ->setConstructorArgs([])
            ->getMockForTrait();
        $this->app->instance(Tools::class, $this->tools);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->tools);
    }

    public function testBackupTable(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
