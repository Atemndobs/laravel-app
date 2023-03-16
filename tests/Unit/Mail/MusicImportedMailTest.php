<?php

namespace Tests\Unit\Mail;

use App\Mail\MusicImportedMail;
use Tests\TestCase;

/**
 * Class MusicImportedMailTest.
 *
 * @covers \App\Mail\MusicImportedMail
 */
final class MusicImportedMailTest extends TestCase
{
    private MusicImportedMail $musicImportedMail;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->musicImportedMail = new MusicImportedMail();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->musicImportedMail);
    }

    public function testBuild(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
