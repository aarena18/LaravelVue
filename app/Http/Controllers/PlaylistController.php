<?php

namespace App\Http\Controllers;

use App\Events\PlaylistCreated;
use App\Http\Requests\PlaylistRequest;
use App\Models\Playlist;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $playlists = Playlist::withCount('tracks')
            ->with(['tracks' => function ($query) {
                $query->select('tracks.id', 'tracks.image')->limit(1);
            }])
            ->get()
            ->map(function ($playlist) {
                $playlist->first_track_image = $playlist->tracks->first()?->image;
                unset($playlist->tracks);
                return $playlist;
            });

        return Inertia::render('Playlists/Index', [
            'playlists' => $playlists,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tracks = Track::where('is_visible', true)->get();

        return Inertia::render('Playlists/Create', [
            'tracks' => $tracks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlaylistRequest $request)
    {
        $tracks = Track::whereIn('slug', $request->tracks)->where('is_visible', true)->get();

        if (count($request->tracks) !== $tracks->count()) {
            throw ValidationException::withMessages(['tracks' => 'Tracks not found']);
        }

        $playlist = Playlist::create([
            'slug' => 'ply-' . Str::uuid(),
            'user_id' => $request->user()->id,
            'title' => $request->title,
        ]);

        $playlist->tracks()->attach($tracks->pluck('id'));

        PlaylistCreated::dispatch($playlist);

        return redirect()->route('playlists.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Playlist $playlist)
    {
        $playlist->load('tracks');
        $tracks = Track::where('is_visible', true)->get();

        return Inertia::render('Playlists/Edit', [
            'playlist' => $playlist,
            'tracks' => $tracks,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlaylistRequest $request, Playlist $playlist)
    {
        $tracks = Track::whereIn('slug', $request->tracks)->where('is_visible', true)->get();

        if (count($request->tracks) !== $tracks->count()) {
            throw ValidationException::withMessages(['tracks' => 'Tracks not found']);
        }

        // $playlist->update($request->validated());

        $playlist->title = $request->title;
        $playlist->save();

        $playlist->tracks()->sync($tracks->pluck('id'));

        return redirect()->route('playlists.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Playlist $playlist)
    {
        $playlist->delete();

        return redirect()->back();
    }

    /**
     * Attach a track to a playlist.
     */
    public function attachTrack(Playlist $playlist, Track $track)
    {
        // Check if track is already in the playlist
        if (!$playlist->tracks()->where('track_id', $track->id)->exists()) {
            $playlist->tracks()->attach($track->id);
        }

        return redirect()->back();
    }

    /**
     * Detach a track from a playlist.
     */
    public function detachTrack(Playlist $playlist, Track $track)
    {
        $playlist->tracks()->detach($track->id);

        return redirect()->back();
    }
}
