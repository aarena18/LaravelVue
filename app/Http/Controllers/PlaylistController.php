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
    public function index(Request $request)
    {
        // Only return playlists that belong to the authenticated user
        $user = $request->user();

        $query = Playlist::withCount('tracks')
            ->with(['tracks' => function ($query) {
                $query->select('tracks.id', 'tracks.image')->limit(1);
            }]);

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->whereNull('id'); // return empty
        }

        $playlists = $query->get()
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
    public function edit(Request $request, Playlist $playlist)
    {
        // Ensure the authenticated user owns this playlist
        if ($request->user()->id !== $playlist->user_id) {
            abort(403);
        }

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
        // Ensure owner
        if ($request->user()->id !== $playlist->user_id) {
            abort(403);
        }
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
    public function destroy(Request $request, Playlist $playlist)
    {
        // Ensure owner
        if ($request->user()->id !== $playlist->user_id) {
            abort(403);
        }

        // Detach all tracks from the playlist to avoid foreign key constraint
        $playlist->tracks()->detach();

        $playlist->delete();

        return redirect()->back();
    }

    /**
     * Attach a track to a playlist.
     */
    public function attachTrack(Request $request, Playlist $playlist, Track $track)
    {
        if ($request->user()->id !== $playlist->user_id) {
            abort(403);
        }

        // Check if track is already in the playlist
        if (!$playlist->tracks()->where('track_id', $track->id)->exists()) {
            $playlist->tracks()->attach($track->id);
        }

        return redirect()->back();
    }

    /**
     * Detach a track from a playlist.
     */
    public function detachTrack(Request $request, Playlist $playlist, Track $track)
    {
        if ($request->user()->id !== $playlist->user_id) {
            abort(403);
        }

        $playlist->tracks()->detach($track->id);

        return redirect()->back();
    }
}
