<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TrackController extends Controller
{
    public function index(Request $request)
    {
        $tracks = Track::where('is_visible', true)->get();

        $userPlaylists = [];
        $trackPlaylistIds = [];

        if ($request->user()) {
            $userPlaylists = Playlist::where('user_id', $request->user()->id)
                ->select('id', 'slug', 'title')
                ->get();

            // Get all playlist IDs that contain each track
            $trackPlaylistMap = Playlist::where('user_id', $request->user()->id)
                ->with('tracks:id')
                ->get()
                ->flatMap(function ($playlist) {
                    return $playlist->tracks->map(function ($track) use ($playlist) {
                        return ['track_id' => $track->id, 'playlist_id' => $playlist->id];
                    });
                })
                ->groupBy('track_id')
                ->map(function ($items) {
                    return $items->pluck('playlist_id')->toArray();
                })
                ->toArray();

            $trackPlaylistIds = $trackPlaylistMap;
        }

        return Inertia::render('Tracks/Index', [
            'tracks' => $tracks,
            'userPlaylists' => $userPlaylists,
            'trackPlaylistIds' => $trackPlaylistIds,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tracks/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image'],
            'music' => ['required', 'file', 'mimes:wav,mp3'],
        ]);

        $slug = 'trk-' . Str::uuid();

        $imagePath = $request->image->storeAs('tracks/images', $slug . '.' . $request->image->extension());
        $musicPath = $request->music->storeAs('tracks/musics', $slug . '.' . $request->music->extension());

        Track::create([
            'slug' => $slug,
            'title' => $request->title,
            'artist' => $request->artist,
            'image' => $imagePath,
            'audio' => $musicPath,
        ]);

        // return redirect()->back();
        return redirect()->route('tracks.index');
    }

    public function edit(Track $track)
    {
        return Inertia::render('Tracks/Edit', [
            'track' => $track,
        ]);
    }

    public function update(Request $request, Track $track)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['required', 'string', 'max:255'],
        ]);

        $track->title = $request->title;
        $track->artist = $request->artist;
        $track->save();

        return redirect()->route('tracks.index');
    }

    public function destroy(Track $track)
    {
        // Storage::delete([$track->image, $track->audio]);

        $track->delete();

        return redirect()->back();
    }
}
