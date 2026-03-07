<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Client::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:clients',
            'uuid' => 'required|unique:clients',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $client = Client::create($request->only(['name', 'uuid']));

        Cache::forget('clients');

        return response()->json($client, 201);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $client = Client::where('uuid', $uuid)->first();

        if (! $client) {
            return response()->json(['error' => 'Client not found.'], 404);
        }

        $this->authorize('update', $client);

        if ($request->has('modpacks')) {
            $validator = Validator::make($request->all(), [
                'modpacks' => 'array',
                'modpacks.*' => 'integer|exists:modpacks,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $client->modpacks()->sync($request->input('modpacks', []));
        }

        if ($request->has('name')) {
            $client->update($request->only(['name']));
        }

        Cache::forget('clients');

        return response()->json($client);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $client = Client::where('uuid', $uuid)->first();

        if (! $client) {
            return response()->json(['error' => 'Client not found.'], 404);
        }

        $this->authorize('delete', $client);

        $client->modpacks()->detach();
        $client->delete();

        Cache::forget('clients');

        return response()->json(['success' => 'Client deleted.']);
    }
}
