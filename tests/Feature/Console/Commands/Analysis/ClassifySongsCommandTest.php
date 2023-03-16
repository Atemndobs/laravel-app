<?php

namespace Tests\Feature\Console\Commands\Analysis;

use App\Console\Commands\Analysis\ClassifySongsCommand;
use Tests\TestCase;

/**
 * Class ClassifySongsCommandTest.
 *
 * @covers \App\Console\Commands\Analysis\ClassifySongsCommand
 */
final class ClassifySongsCommandTest extends TestCase
{
    private ClassifySongsCommand $classifySongsCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->classifySongsCommand = new ClassifySongsCommand();
        $this->app->instance(ClassifySongsCommand::class, $this->classifySongsCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->classifySongsCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('song:classify')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
