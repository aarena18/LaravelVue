<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    /**
     * Generate a new API key for the authenticated user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $apiKey = ApiKey::create([
            'user_id' => $request->user()->id,
            'key' => ApiKey::generateKey(),
            'name' => $request->name ?? 'API Key',
        ]);

        return redirect()->back()->with('api_key', $apiKey->key);
    }

    /**
     * Delete an API key.
     */
    public function destroy(Request $request, ApiKey $apiKey)
    {
        // Ensure the user owns this API key
        if ($apiKey->user_id !== $request->user()->id) {
            abort(403);
        }

        $apiKey->delete();

        return redirect()->back();
    }
}
