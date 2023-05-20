<?php

namespace App\Services\Birdy;

use AllowDynamicProperties;
use App\Models\MatchCriterion;
use App\Models\User;
use Illuminate\Support\Facades\Log;

#[AllowDynamicProperties] class MatchCriteriaService
{
    // set default values
    protected float $bpm = 100;
    protected float $bpmMin = 50;
    protected float $bpmMax = 200;
    protected float $happy = 60;
    protected float $sad = 40;
    protected string $key = 'A';
    protected string $scale = 'minor';
    protected float $energy = 80;
    protected string $mood = "happy";
    protected float $danceability = 80;
    protected float $aggressiveness = 50;
    protected string $ip = '';
    protected string $sessionToken = '';
    protected ?\Illuminate\Contracts\Auth\Authenticatable $user;

    // set Ipp and session token in constructor
    public function __construct()
    {
        $this->ip = request()->ip();
        $this->sessionToken = session()->getId();
        $this->user = auth()->user();
        if (is_null($this->user)) {
            $this->user = User::query()->where('role_id', 1)->get()->first();
        }
    }

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


        $matchCriteriaExist = MatchCriterion::query()->where('session_token', $this->sessionToken)
            ->orWhere('ip', $this->ip)
            ->get()->first();
        if (is_null($matchCriteriaExist)) {
            $matchCriteria = new MatchCriterion();
        }else{
            $matchCriteria = $matchCriteriaExist;
        }
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

        if ($matchCriteriaExist) {
            $matchCriteria->date_updated = now();
            $matchCriteria->update();

        } else {
            $matchCriteria->date_created = now();
            $matchCriteria->date_updated = now();
            $matchCriteria->save();
        }

    }

    /**
     * @return array
     */
    public function getCriteria(): array
    {
        $ip = $this->ip;
        $sessionToken = $this->sessionToken;
        $matchCriteria = MatchCriterion::query()->where('ip', $ip)
            ->orWhere('session_token', $sessionToken)
            ->first();

        if (!$matchCriteria) {
            $this->setDefaultCriteria();
            $matchCriteria = MatchCriterion::query()->where('ip', $ip)
                ->orWhere('session_token', $sessionToken)
                ->first();
        }

        Log::info($matchCriteria->toArray());

        return $matchCriteria->toArray();
    }

    /**
     * @return void
     */
    public function setDefaultCriteria(): void
    {
        $criteria = [
            'bpm' => $this->bpm,
            'bpmMin' => $this->bpmMin,
            'bpmMax' => $this->bpmMax,
            'happy' => $this->happy,
            'sad' => $this->sad,
            'key' => $this->key,
            'scale' => $this->scale,
            'energy' => $this->energy,
            'mood' => $this->mood,
            'danceability' => $this->danceability,
            'aggressiveness' => $this->aggressiveness,
            'ip' => $this->ip,
            'sessionToken' => $this->sessionToken,
            'status' => 'active',
            'sort' => 0
        ];
        $this->setCriteria($criteria);
    }

}