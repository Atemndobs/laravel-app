<?php

namespace Tests\Unit\Services;

use App\Services\MultipleSheeExport;
use Tests\TestCase;

/**
 * Class MultipleSheeExportTest.
 *
 * @covers \App\Services\MultipleSheeExport
 */
final class MultipleSheeExportTest extends TestCase
{
    private MultipleSheeExport $multipleSheeExport;

    private array $data;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [];
        $this->multipleSheeExport = new MultipleSheeExport($this->data);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->multipleSheeExport);
        unset($this->data);
    }

    public function testSheets(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
