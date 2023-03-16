<?php

namespace Tests\Unit\Services;

use App\Services\UsersExportService;
use Tests\TestCase;

/**
 * Class UsersExportServiceTest.
 *
 * @covers \App\Services\UsersExportService
 */
final class UsersExportServiceTest extends TestCase
{
    private UsersExportService $usersExportService;

    private mixed $data;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->data = null;
        $this->usersExportService = new UsersExportService($this->data);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->usersExportService);
        unset($this->data);
    }

    public function testCollection(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testHeadings(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
