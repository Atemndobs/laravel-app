<?php

namespace Tests\Unit\Services;

use App\Services\UploadService;
use ReflectionClass;
use Tests\TestCase;

/**
 * Class UploadServiceTest.
 *
 * @covers \App\Services\UploadService
 */
final class UploadServiceTest extends TestCase
{
    private UploadService $uploadService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->uploadService = new UploadService();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->uploadService);
    }

    public function testGetDeletables(): void
    {
        $expected = [];
        $property = (new ReflectionClass(UploadService::class))
            ->getProperty('deletables');
        $property->setValue($this->uploadService, $expected);
        $this->assertSame($expected, $this->uploadService->getDeletables());
    }

    public function testSetDeletables(): void
    {
        $expected = [];
        $property = (new ReflectionClass(UploadService::class))
            ->getProperty('deletables');
        $this->uploadService->setDeletables($expected);
        $this->assertSame($expected, $property->getValue($this->uploadService));
    }

    public function testAddDeletables(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testUploadSong(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testBatchUpload(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testImportSongs(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testFillSong(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
