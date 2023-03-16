<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\ClassificationController;
use App\Models\Song;
use App\Services\ClassifyService;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class ClassificationControllerTest.
 *
 * @covers \App\Http\Controllers\Api\ClassificationController
 */
final class ClassificationControllerTest extends TestCase
{
    private ClassificationController $classificationController;

    private ClassifyService|Mock $classifyService;

    private Song|Mock $song;

    private Request|Mock $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->classifyService = Mockery::mock(ClassifyService::class);
        $this->song = Mockery::mock(Song::class);
        $this->request = Mockery::mock(Request::class);
        $this->classificationController = new ClassificationController($this->classifyService, $this->song, $this->request);
        $this->app->instance(ClassificationController::class, $this->classificationController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->classificationController);
        unset($this->classifyService);
        unset($this->song);
        unset($this->request);
    }

    public function testClassify(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }

    public function testAnalyze(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }

    public function testFindByTitle(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
