<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mod;
use App\Models\Modversion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ModversionController extends Controller
{
    public function show(string $slug, string $version): JsonResponse
    {
        if (config('solder.disable_mod_api')) {
            return response()->json(['error' => 'Mod API has been disabled'], 404);
        }

        $mod = Cache::remember('mod:'.$slug, now()->addMinutes(5), function () use ($slug) {
            return Mod::with('versions')->where('name', $slug)->first();
        });

        if (! $mod) {
            return response()->json(['error' => 'Mod does not exist'], 404);
        }

        $modVersion = $mod->versions()->where('version', $version)->first();

        if (! $modVersion) {
            return response()->json(['error' => 'Mod version does not exist'], 404);
        }

        $response = $modVersion->only([
            'id',
            'md5',
            'filesize',
            'url',
        ]);

        return response()->json($response);
    }

    public function store(Request $request, string $slug): JsonResponse
    {
        $this->authorize('create', Modversion::class);

        $mod = Mod::where('name', $slug)->first();

        if (! $mod) {
            return response()->json(['error' => 'Mod not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'version' => 'required',
            'md5' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if ($mod->versions()->where('version', $request->input('version'))->exists()) {
            return response()->json(['error' => 'Version already exists for this mod.'], 422);
        }

        $modversion = $mod->versions()->create($request->only(['version', 'md5', 'filesize', 'notes']));

        Cache::forget('mod:'.$slug);
        Cache::forget('mods');

        return response()->json($modversion, 201);
    }

    public function destroy(string $slug, string $version): JsonResponse
    {
        $this->authorize('delete', Modversion::class);

        $mod = Mod::where('name', $slug)->first();

        if (! $mod) {
            return response()->json(['error' => 'Mod not found.'], 404);
        }

        /** @var Modversion|null $modversion */
        $modversion = $mod->versions()->where('version', $version)->first();

        if (! $modversion) {
            return response()->json(['error' => 'Mod version not found.'], 404);
        }

        if ($modversion->builds()->exists()) {
            return response()->json([
                'error' => 'Mod version is in use by '.$modversion->builds()->count().' build(s) and cannot be deleted.',
            ], 409);
        }

        $modversion->delete();

        Cache::forget('mod:'.$slug);
        Cache::forget('mods');

        return response()->json(['success' => 'Mod version deleted.']);
    }
}
