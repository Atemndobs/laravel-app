<?php

namespace Tests\Feature\Http\Controllers\Feeds;

use App\Http\Controllers\Feeds\FeedsController;
use App\Services\Crawler\SpatieCrawlerService;
use App\Services\Feeds\ProxyCrawlService;
use App\Services\Feeds\StrapiService;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class FeedsControllerTest.
 *
 * @covers \App\Http\Controllers\Feeds\FeedsController
 */
final class FeedsControllerTest extends TestCase
{
    private FeedsController $feedsController;

    private SpatieCrawlerService|Mock $spatieCrawlerService;

    private ProxyCrawlService|Mock $proxyCrawlService;

    private StrapiService|Mock $strapiService;

    private Request|Mock $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->spatieCrawlerService = Mockery::mock(SpatieCrawlerService::class);
        $this->proxyCrawlService = Mockery::mock(ProxyCrawlService::class);
        $this->strapiService = Mockery::mock(StrapiService::class);
        $this->request = Mockery::mock(Request::class);
        $this->feedsController = new FeedsController($this->spatieCrawlerService, $this->proxyCrawlService, $this->strapiService, $this->request);
        $this->app->instance(FeedsController::class, $this->feedsController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->feedsController);
        unset($this->spatieCrawlerService);
        unset($this->proxyCrawlService);
        unset($this->strapiService);
        unset($this->request);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testSort(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testCloud(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testSamleCreate(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testNew(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testGetExtractById(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testClassify(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
