<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlaylistApiController extends Controller
{
    /**
     * Get all playlists for the authenticated user.
     */
    public function index(Request $request)
    {
        $playlists = $request->user()
            ->playlists()
            ->withCount('tracks')
            ->with(['tracks' => function ($query) {
                $query->select('tracks.id', 'tracks.slug', 'tracks.title', 'tracks.artist', 'tracks.image');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $playlists->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'slug' => $playlist->slug,
                    'title' => $playlist->title,
                    'tracks_count' => $playlist->tracks_count,
                    'tracks' => $playlist->tracks,
                    'created_at' => $playlist->created_at,
                    'updated_at' => $playlist->updated_at,
                ];
            }),
        ]);
    }

    /**
     * Get a specific playlist for the authenticated user.
     */
    public function show(Request $request, string $slug)
    {
        $playlist = $request->user()
            ->playlists()
            ->where('slug', $slug)
            ->withCount('tracks')
            ->with(['tracks' => function ($query) {
                $query->select('tracks.id', 'tracks.slug', 'tracks.title', 'tracks.artist', 'tracks.image', 'tracks.audio');
            }])
            ->first();

        if (!$playlist) {
            return response()->json([
                'success' => false,
                'message' => 'Playlist not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $playlist->id,
                'slug' => $playlist->slug,
                'title' => $playlist->title,
                'tracks_count' => $playlist->tracks_count,
                'tracks' => $playlist->tracks,
                'created_at' => $playlist->created_at,
                'updated_at' => $playlist->updated_at,
            ],
        ]);
    }
}
