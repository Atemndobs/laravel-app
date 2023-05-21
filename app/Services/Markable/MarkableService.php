<?php

namespace App\Services\Markable;

use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use Maize\Markable\Models\Favorite;
use Maize\Markable\Models\Like;
use Maize\Markable\Models\Reaction;

class MarkableService
{
    public function markBySlug($slug, $type, $user_id, $reaction = null )
    {

        // Benchmark $this->clearAllMarks and $this->clearAllMarksOpt
        Benchmark::dd(function () use ($slug, $type, $user_id, $reaction) {
         // $this->clearAllMarks($slug);
           //$this->clearAllMarksOrganize($slug);
            // $this->clearAllMarksOpt($slug);
            //$this->clearAllMarksOption2($slug);
           // $this->clearAllMarksParty($slug);
            $this->clearAllMarksDelete($slug);
        }, 1 );

        //dd(User::all('id')->toArray());// 4,5,6,7,8
        $song = Song::query()->where('slug', $slug)->first();
        $user = User::query()->find($user_id) ?? User::query()->find(5);

        $allTypes = Song::marks();
       // dd($allTypes);
        foreach ($allTypes as $MarkType) {
            $typename = class_basename($MarkType);
           // $MarkType::remove($song, $user);
            if ($typename == 'Reaction') {
                $MarkType::add($song, $user, $reaction);
            }
            elseif ($type == $typename) {
                try {
                    $MarkType::add($song, $user);
                } catch (\Exception $e) {
                    dump($e->getMessage());
                    throw new \Exception('Type not found : ' . $type);
                }
            }
        }

        dd([
            'likes' => Like::count($song),
            'Favs' => Favorite::count($song),
            'hearts' => Reaction::count($song, 'heart'),
            'thumbs_up' => Reaction::count($song, 'thumbs_up'),
            'thumbs_down' => Reaction::count($song, 'thumbs_down'),
            'person_raising_hand' => Reaction::count($song, 'person_raising_hand'),
        ]);
    }

    public function clearAllMarks($slug)
    {
        $song = Song::query()->where('slug', $slug)->first();
        $allTypes = Song::marks();
        $reactions = Reaction::all('value')->pluck('value')->toArray();

        foreach ($allTypes as $MarkType) {
            $typename = class_basename($MarkType);
            foreach (User::all() as $user) {
                if ($typename == 'Reaction') {
                    foreach ($reactions as $reaction) {
                        $MarkType::remove($song, $user, $reaction);
                    }
                }
                else {
                    $MarkType::remove($song, $user);
                }
                $MarkType::remove($song, $user);
            }
        }
        $results =  [
            'likes' => Like::count($song),
            'Favs' => Favorite::count($song),
            'hearts' => Reaction::count($song, 'heart'),
            'thumbs_up' => Reaction::count($song, 'thumbs_up'),
            'thumbs_down' => Reaction::count($song, 'thumbs_down'),
            'person_raising_hand' => Reaction::count($song, 'person_raising_hand'),
        ];

        dump($results);
    }

    public function clearAllMarksOpt($slug)
    {
        $song = Song::query()->where('slug', $slug)->first();
        $allTypes = Song::marks();

        DB::transaction(function () use ($allTypes, $song) {
            User::cursor()->each(function ($user) use ($allTypes, $song) {
                foreach ($allTypes as $MarkType) {
                    $MarkType::remove($song, $user);
                }
            });
        });

        return [
            'likes' => Like::count($song),
            'Favs' => Favorite::count($song),
            'hearts' => Reaction::count($song, 'heart'),
            'thumbs_up' => Reaction::count($song, 'thumbs_up'),
            'thumbs_down' => Reaction::count($song, 'thumbs_down'),
            'person_raising_hand' => Reaction::count($song, 'person_raising_hand'),
        ];
    }

    public function clearAllMarksOption2($slug)
    {
        $song = Song::with('users')->where('slug', $slug)->first();
        $allTypes = Song::marks();
        $users = $song->users->map(function($user) use($song, $allTypes) {
            foreach ($allTypes as $MarkType) {
                $MarkType::remove($song, $user);
            }
        });
        return [
            'likes' => Like::count($song),
            'Favs' => Favorite::count($song),
            'hearts' => Reaction::count($song, 'heart'),
            'thumbs_up' => Reaction::count($song, 'thumbs_up'),
            'thumbs_down' => Reaction::count($song, 'thumbs_down'),
            'person_raising_hand' => Reaction::count($song, 'person_raising_hand'),
        ];
    }

    public function clearAllMarksOrganize($slug)
    {
        $song = Song::query()->where('slug', $slug)->first();
        $allTypes = Song::marks();
        $reactions = Reaction::all('value')->pluck('value')->toArray();

        User::chunk(1000, function($users) use ($allTypes, $song, $reactions) {
            foreach ($users as $user) {
                foreach ($allTypes as $MarkType) {
                    $typename = class_basename($MarkType);
                    if ($typename == 'Reaction') {
                        foreach ($reactions as $reaction) {
                            $MarkType::remove($song, $user, $reaction);
                        }
                    } else {
                        $MarkType::remove($song, $user);
                    }
                }
            }
        });

        return [
            'likes' => Like::count($song),
            'Favs' => Favorite::count($song),
            'hearts' => Reaction::count($song, 'heart'),
            'thumbs_up' => Reaction::count($song, 'thumbs_up'),
            'thumbs_down' => Reaction::count($song, 'thumbs_down'),
            'person_raising_hand' => Reaction::count($song, 'person_raising_hand'),
        ];

        dump($results);
    }

    public function clearAllMarksParty($slug)
    {
        $song = Song::query()->where('slug', $slug)->first();
        $allTypes = Song::marks();
        $reactions = Reaction::all('value')->pluck('value')->toArray();

        User::chunk(1000, function($users) use ($allTypes, $song, $reactions) {
            foreach ($users as $user) {
                foreach ($allTypes as $MarkType) {
                    $typename = class_basename($MarkType);
                    if ($typename == 'Reaction') {
                        foreach ($reactions as $reaction) {
                            $MarkType::remove($song, $user, $reaction);
                        }
                    } else {
                        $MarkType::remove($song, $user);
                    }
                }
            }
        });

        $results = [
            'likes' => Like::where('markable_id', $song->id)->count(),
            'favs' => Favorite::where('markable_id', $song->id)->count(),
            'hearts' => Reaction::where('markable_id', $song->id)->where('value', 'heart')->count(),
            'thumbs_up' => Reaction::where('markable_id', $song->id)->where('value', 'thumbs_up')->count(),
            'thumbs_down' => Reaction::where('markable_id', $song->id)->where('value', 'thumbs_down')->count(),
            'person_raising_hand' => Reaction::query()->where('markable_id', $song->id)->where('value', 'person_raising_hand')->count(),
        ];
        dump($results);
    }

}
