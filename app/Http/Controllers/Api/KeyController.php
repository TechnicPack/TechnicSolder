<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Key;
use Illuminate\Http\JsonResponse;

class KeyController extends Controller
{
    public function verify(string $key): JsonResponse
    {
        $key = Key::where('api_key', $key)->first();

        if (! $key) {
            return response()->json(['error' => 'Invalid key provided.'], 403);
        }

        return response()->json([
            'valid' => 'Key validated.',
            'name' => $key->name,
            'created_at' => $key->created_at,
        ]);
    }
}
