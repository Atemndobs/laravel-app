<?php

namespace App\Services\Scraper;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;

class SpotifyMusicService
{

    public function playlist(string $playlistName)
    {
        // 11171774669
        $playlists  = Spotify::userPlaylists('11171774669')->get()['items'];

      //  dd($playlistName);
        $playlistDetails = $this->getItemByName($playlists, $playlistName);

        return $playlistDetails;
    }

    public function userId()
    {
        return Spotify::me()->id;
    }

    public function getItemByName($array, $name) {
        $filteredArray = array_filter($array, function ($item) use ($name) {
            return $item['name'] === $name;
        });

        // Retrieve the first matching item
        $result = reset($filteredArray);

        return $result !== false ? $result : null;
    }

    public function getPlalistNames(array $playlists)
    {
        $names = [];
        foreach ($playlists as $playlist) {
            $names[] = $playlist['name'];
        }
        return $names;
    }

    public function getAllPlaylists()
    {
        return Spotify::userPlaylists('11171774669')->get()['items'];
    }

    public function getPlaylistSongs(string $playlistId)
    {
        return Spotify::playlistTracks($playlistId)->get()['items'];
    }

    public function getPlaylistByName(string $playlistName)
    {
        return Spotify::searchPlaylists($playlistName)->get();
    }

}
