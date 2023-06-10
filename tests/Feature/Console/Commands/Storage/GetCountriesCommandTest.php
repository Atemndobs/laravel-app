<?php

namespace Tests\Feature\Console\Commands\Storage;

use App\Console\Commands\Storage\GetCountriesCommand;
use Tests\TestCase;

/**
 * Class GetCountriesCommandTest.
 *
 * @covers \App\Console\Commands\Storage\GetCountriesCommand
 */
final class GetCountriesCommandTest extends TestCase
{
    private GetCountriesCommand $getCountriesCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->getCountriesCommand = new GetCountriesCommand();
        $this->app->instance(GetCountriesCommand::class, $this->getCountriesCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->getCountriesCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:get-countries-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
