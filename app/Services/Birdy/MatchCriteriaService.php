<?php

namespace App\Services\Birdy;

use AllowDynamicProperties;
use App\Models\MatchCriterion;
use Illuminate\Support\Facades\Log;

#[AllowDynamicProperties] class MatchCriteriaService
{
    // set default values
    protected float $bpm = 0;
    protected float $bpmMin = 0;
    protected float $bpmMax = 0;
    protected float $happy = 0;
    protected float $sad = 0;
    protected string $key = '';
    protected string $scale = '';
    protected float $energy = 0;
    protected string $mood = "";
    protected float $danceability = 0;
    protected float $aggressiveness = 0;
    protected string $ip = '';
    protected string $sessionToken = '';
    
    public function setCriteria(array $criteria): void
    {
        $this->bpm = $criteria['bpm'];
        $this->bpmMin = $criteria['bpmMin'];
        $this->bpmMax = $criteria['bpmMax'];
        $this->happy = $criteria['happy'];
        $this->sad = $criteria['sad'];
        $this->key = $criteria['key'];
        // split $key into $key and $scale
        if (str_contains($this->key, ' ')) {
            $keyArray = explode(' ', $this->key);
            $this->key = $keyArray[0];
            $this->scale = $keyArray[1];
        }

        $this->energy = $criteria['energy'];
        $this->mood = $criteria['mood'];
        $this->danceability = $criteria['danceability'];
        $this->aggressiveness = $criteria['aggressiveness'];
        $this->ip = $criteria['ip'];
        $this->sessionToken = $criteria['sessionToken'];

        // check if session token exists
        if (empty($this->sessionToken)) {
            $this->sessionToken = session()->getId();
        }
        $matchCriteria = new MatchCriterion();
        $matchCriteria->bpm = $this->bpm;
        $matchCriteria->bpm_min = $this->bpmMin;
        $matchCriteria->bpm_max = $this->bpmMax;
        $matchCriteria->happy = $this->happy;
        $matchCriteria->sad = $this->sad;
        $matchCriteria->key = $this->key;
        $matchCriteria->scale = $this->scale;
        $matchCriteria->energy = $this->energy;
        $matchCriteria->mood = $this->mood;
        $matchCriteria->danceability = $this->danceability;
        $matchCriteria->aggressiveness = $this->aggressiveness;
        $matchCriteria->ip = $this->ip;
        $matchCriteria->session_token = $this->sessionToken;

        $matchCriteriaExist = MatchCriterion::query()->where('session_token', $this->sessionToken)
            ->orWhere('ip', $this->ip)
            ->first();

        // if session exists, update it
        if ($matchCriteriaExist) {
            $matchCriteria->update();
        } else {
            $matchCriteria->save();
        }

    }

    /**
     * @param string $ip
     * @param string $sessionToken
     * @return array
     */
    public function getCriteria(string $ip, string $sessionToken): array
    {
        $matchCriteria = MatchCriterion::query()->where('ip', $ip)
            ->orWhere('session_token', $sessionToken)
            ->first();

        Log::info($matchCriteria->toArray());

        return $matchCriteria->toArray();
    }

}