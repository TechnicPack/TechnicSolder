<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class KeyController extends Controller
{
    public function verify(string $key): JsonResponse
    {
        $keys = Cache::remember('keys', now()->addMinutes(1), fn () => Key::all());

        $match = $keys->first(fn (Key $k) => hash_equals($k->api_key, $key));

        if (! $match) {
            return response()->json(['error' => 'Invalid key provided.'], 403);
        }

        return response()->json([
            'valid' => 'Key validated.',
            'name' => $match->name,
        ]);
    }
}
