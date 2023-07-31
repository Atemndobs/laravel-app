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
    protected string $mood = "happy";
    protected string $genre = "Afrobeat";
    protected float $energy = 80;
    protected float $danceability = 80;
    protected float $aggressiveness = 80;
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

    public function setCriteria(array $criteria) : MatchCriterion
    {
        $this->bpm = $criteria['bpm'] ?? 100;
        $this->bpmMin = $criteria['bpmMin'] ?? 50;
        $this->bpmMax = $criteria['bpmMax'] ?? 200;
        $this->happy = $criteria['happy'] ?? 0.00;
        $this->sad = $criteria['sad'] ?? 0.00;
        $this->key = $criteria['key'] ?? 'A';
        if (str_contains($this->key, ' ')) {
            $keyArray = explode(' ', $this->key);
            $this->key = $keyArray[0];
            $this->scale = $keyArray[1];
        }
        $this->genre = $criteria['genre'] ?? [];
        $this->energy = $criteria['energy'] ?? 0.00;
        $this->mood = $criteria['mood'] ?? 'happy';
        $this->danceability = $criteria['danceability'] ?? 0.00;
        $this->aggressiveness = $criteria['aggressiveness'] ?? 0.00 ;
        $this->ip = $criteria['ip'] ?? '';
        $this->sessionToken = $criteria['sessionToken'] ?? '';
        $this->bpm_range = $criteria['bpm_range'] ?? 0.00;

        // check if session token exists
        if (empty($this->sessionToken)) {
            $this->sessionToken = session()->getId();
        }

        $matchCriteriaExist = MatchCriterion::query()->where('session_token', $this->sessionToken)
            ->where('ip', $this->ip)
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
        $matchCriteria->genre = $this->genre;
        $matchCriteria->energy = $this->energy;
        $matchCriteria->mood = $this->mood;
        $matchCriteria->danceability = $this->danceability;
        $matchCriteria->aggressiveness = $this->aggressiveness;
        $matchCriteria->ip = $this->ip;
        $matchCriteria->session_token = $this->sessionToken;
        $matchCriteria->bpm_range = $this->bpm_range;

        if ($matchCriteriaExist) {
            $matchCriteria->date_updated = now();
            $matchCriteria->update();

        } else {
            $matchCriteria->date_created = now();
            $matchCriteria->date_updated = now();
            $matchCriteria->save();
        }

        Log::warning('Setting Match Criteria______________________________________');
        Log::info(json_encode($matchCriteria->toArray(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $matchCriteria;
    }


    /**
     * @return MatchCriterion
     */
    public function getCriteria() : MatchCriterion
    {
        $sessionToken = $this->sessionToken;
        $ip = $this->ip;
        /** @var MatchCriterion $matchCriteria */
        $matchCriteria = MatchCriterion::query()->where('session_token', $sessionToken)
            ->orWhere('ip', $ip)
            ->first();

        if (!$matchCriteria) {
            $matchCriteria = $this->setDefaultCriteria();
        }

        return $matchCriteria;
    }

    /**
     * @return MatchCriterion
     */
    public function setDefaultCriteria(): MatchCriterion
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
            'genre' => $this->genre,
            'danceability' => $this->danceability,
            'aggressiveness' => $this->aggressiveness,
            'ip' => $this->ip,
            'sessionToken' => $this->sessionToken,
            'status' => 'active',
            'sort' => 0,
            'bpm_range' => 1
        ];

        return $this->setCriteria($criteria);
    }

    public function removePlayedSong(mixed $id): ?string
    {
        $matchCriteria = $this->getCriteria();
        $playedSongs = $matchCriteria->played_songs;
        if (is_null($playedSongs)) {
            $playedSongs = [];
        }

        $playedSongs = explode(',', $playedSongs);
        $playedSongs = array_diff($playedSongs, [$id]);
        $playedSongs = implode(',', $playedSongs);
        $matchCriteria->played_songs = $playedSongs;
        $matchCriteria->update();
        return $matchCriteria->played_songs;
    }

    public function removeAllPlayedSongs(): ?string
    {
        $matchCriteria = $this->getCriteria();
        $matchCriteria->played_songs = null;
        $matchCriteria->update();
        return $matchCriteria->played_songs;
    }

}