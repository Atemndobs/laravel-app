<?php

namespace Tests\Unit\Services\Birdy;

use App\Services\Birdy\UserImportService;
use Tests\TestCase;

/**
 * Class UserImportServiceTest.
 *
 * @covers \App\Services\Birdy\UserImportService
 */
final class UserImportServiceTest extends TestCase
{
    private UserImportService $userImportService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->userImportService = new UserImportService();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->userImportService);
    }

    public function testGetUserData(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testPrepareRequest(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRequestDev(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetFields(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
