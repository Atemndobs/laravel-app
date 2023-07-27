<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Birdy\MatchCriteriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MatchCriteriaController extends Controller
{
    /**
     * @var Request
     */
    public Request $request;

    public MatchCriteriaService $matchCriteriaService;

    /**
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->matchCriteriaService = new MatchCriteriaService();
    }

    public function getCriteria(): array
    {
        return $this->matchCriteriaService->getCriteria()->toArray();
    }

    public function setCriteria(): JsonResponse
    {
        $criteria = $this->request->all();
        $bpmRange = $this->request->input('bpmRange');
        $bpmMin = null;
        $bpmMax = null;
        if (str_contains($bpmRange, '-')) {
            $bpmRange = explode('-', $bpmRange);
            $bpmMin = $bpmRange[0];
            $bpmMax = $bpmRange[count($bpmRange) - 1];
        }
        $criteria['bpmMin'] = $bpmMin;
        $criteria['bpmMax'] = $bpmMax;

        $ip = $this->request->ip();
        $criteria['ip'] = $ip;
        $sessionToken = $this->request->session()->token();
        $criteria['sessionToken'] = $sessionToken;

        // if genre is null, set default genre as Afrobeat
        if (is_null($criteria['genre'])) {
            $criteria['genre'] = 'Afrobeat';
        }

        Log::info($criteria);

        $this->matchCriteriaService->setCriteria($criteria);
        return response()->json([
            'message' => 'Criteria set successfully',
            'criteria' => $this->matchCriteriaService->getCriteria()->toArray()
        ], 200);
    }
}
