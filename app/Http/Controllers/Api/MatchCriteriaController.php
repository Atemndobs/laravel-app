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
        $criteria['range'] = $this->request->range ?? 1;

        // if genre is null, set default genre as Afrobeat
        if (is_null($criteria['genre'])) {
            $criteria['genre'] = 'Afrobeat';
        }

        Log::warning('Setting Criteria ###########################################');
        Log::info(json_encode($criteria, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->matchCriteriaService->setCriteria($criteria);
        return response()->json([
            'message' => 'Criteria set successfully',
            'criteria' => $this->matchCriteriaService->getCriteria()->toArray()
        ], 200);
    }

    public function clearCriteria()
    {
        $id = $this->request->input('id');
        if (is_null($id)) {
            $this->matchCriteriaService->removeAllPlayedSongs();
        }else {
            $this->matchCriteriaService->removePlayedSong($id);
        }

        return response()->json([
            'message' => 'Criteria cleared successfully',
            'criteria' => $this->matchCriteriaService->getCriteria()->toArray()
        ], 200);
    }
}
