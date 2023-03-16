<?php

namespace Tests\Feature\Console\Commands\Exchange;

use App\Console\Commands\Exchange\CurrencyCommand;
use Tests\TestCase;

/**
 * Class CurrencyCommandTest.
 *
 * @covers \App\Console\Commands\Exchange\CurrencyCommand
 */
final class CurrencyCommandTest extends TestCase
{
    private CurrencyCommand $currencyCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->currencyCommand = new CurrencyCommand();
        $this->app->instance(CurrencyCommand::class, $this->currencyCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->currencyCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
