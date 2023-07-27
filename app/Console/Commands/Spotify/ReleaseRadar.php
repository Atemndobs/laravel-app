<?php

namespace App\Console\Commands\Spotify;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use App\Models\SpotifyAuth;
use App\Services\Birdy\SpotifyService;
use App\Models\SingleRelease;
use App\Services\Scraper\SpotifyMusicService;
use App\Services\Spotify\SpotifyAuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Monolog\Handler\IFTTTHandler;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class ReleaseRadar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:release-radar {--t|time=} {--p|playlist=} {--a|all=} {--o|owner=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the latest Releases from all followed Playlists and save them to ATM Release Radar playlist in Spotify. Time is in hours.
    Options are --time (default 24), --playlist, --all, --owner';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $playlist = $this->option('playlist');
        $all = $this->option('all');
        $owner = $this->option('owner') ?? "Spotify";
        $time = $this->option('time') ?? 24;
        // if time is given in days (input for time contains 'd' at the end), convert it to hours
        if (str_contains($time, 'd')) {
            $time = intval($time) * 24;
        }

        $spotifyMusicService = new SpotifyMusicService();
        $songIds = $spotifyMusicService->getRecentlyAddedSongs($time);
        $spotifyMusicService->addSongToReleaseRadar($songIds);

        dd($sonIds);

        // Run the SpotifyReleasesCommand with the --all option if no playlist is given
        if (!$playlist) {
            $this->info('Getting all playlists...');
            $this->call('spotify:watch', [
                '--all' => true,
                '--owner' => $owner,
            ]);
        }else{
            // Run the SpotifyReleasesCommand with the --playlist option if a playlist is given
            $this->call('spotify:watch', [
                '--playlist' => $playlist,
                '--owner' => $owner,
            ]);
        }
        // Check in the SingleRelease table for releases added since 24 hours if no time is given and collect them
        $recentlyAddedReleases = SingleRelease::query()->where('added_at', '>=', Carbon::now()->subHours($time))->get();
        // if no releases are found, return
        if (!$recentlyAddedReleases) {
            $this->info('No releases added in the last ' . $time . ' hours.');
            return 0;
        }
        // Add them to the ATM Release Radar playlist in Spotify
        $this->info('Adding releases to ATM Release Radar playlist...');


    }

    public function getMyLibrary(string $accessToken)
    {
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
       //  $me = $api->me();
        return $api->getMySavedTracks([
            'limit' => 50,
        ]);
    }
}
