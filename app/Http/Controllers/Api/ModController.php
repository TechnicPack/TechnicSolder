<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ModController extends Controller
{
    public function index(): JsonResponse
    {
        if (config('solder.disable_mod_api')) {
            return response()->json(['error' => 'Mod API has been disabled'], 404);
        }

        $mods = Cache::remember('mods', now()->addMinutes(5), function () {
            return Mod::pluck('pretty_name', 'name');
        });

        return response()->json([
            'mods' => $mods,
        ]);
    }

    public function show(string $slug): JsonResponse
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

        $response = $mod->only([
            'id',
            'name',
            'pretty_name',
            'author',
            'description',
            'link',
        ]);

        $perm = auth('sanctum')->user()?->permission;
        if ($perm?->solder_full || $perm?->mods_manage) {
            $response['notes'] = $mod->notes;
        }

        $response['versions'] = $mod->versions->pluck('version');

        return response()->json($response);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Mod::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:mods',
            'pretty_name' => 'required',
            'link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $mod = Mod::create($request->only(['name', 'pretty_name', 'description', 'author', 'link', 'notes']));

        Cache::forget('mods');

        return response()->json($mod, 201);
    }

    public function update(Request $request, string $slug): JsonResponse
    {
        $mod = Mod::where('name', $slug)->first();

        if (! $mod) {
            return response()->json(['error' => 'Mod not found.'], 404);
        }

        $this->authorize('update', $mod);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|unique:mods,name,'.$mod->id,
            'pretty_name' => 'sometimes|required',
            'link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $oldName = $mod->name;
        $mod->update($request->only(['name', 'pretty_name', 'description', 'author', 'link', 'notes']));

        Cache::forget('mod:'.$oldName);
        if ($oldName !== $mod->name) {
            Cache::forget('mod:'.$mod->name);
        }
        Cache::forget('mods');

        return response()->json($mod);
    }

    public function destroy(string $slug): JsonResponse
    {
        $mod = Mod::where('name', $slug)->first();

        if (! $mod) {
            return response()->json(['error' => 'Mod not found.'], 404);
        }

        $this->authorize('delete', $mod);

        foreach ($mod->versions as $version) {
            $version->builds()->detach();
            $version->delete();
        }

        $mod->delete();

        Cache::forget('mod:'.$slug);
        Cache::forget('mods');

        return response()->json(['success' => 'Mod deleted.']);
    }
}
