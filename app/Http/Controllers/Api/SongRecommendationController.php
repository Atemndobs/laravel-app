<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Song\SongRecommendationService;
use Illuminate\Http\Request;

class SongRecommendationController extends Controller
{
    public function getRecommendation()
    {
        $songId = \request()->songId;
        $k = \request()->k;
        $recommendationService = new SongRecommendationService();
        $recommendation = $recommendationService->getNearestNeighbor($songId, $k);
        return response()->json($recommendation);

    }
}
