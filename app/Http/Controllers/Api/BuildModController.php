<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Build;
use App\Models\Mod;
use App\Models\Modpack;
use App\Models\Modversion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class BuildModController extends Controller
{
    public function store(Request $request, string $slug, string $version): JsonResponse
    {
        $modpack = Modpack::where('slug', $slug)->first();

        if (! $modpack) {
            return response()->json(['error' => 'Modpack not found.'], 404);
        }

        $this->authorize('update', [Build::class, $modpack]);

        /** @var Build|null $build */
        $build = $modpack->builds()->where('version', $version)->first();

        if (! $build) {
            return response()->json(['error' => 'Build not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'mod_slug' => 'required|string',
            'mod_version' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $mod = Mod::where('name', $request->input('mod_slug'))->first();

        if (! $mod) {
            return response()->json(['error' => 'Mod not found.'], 404);
        }

        /** @var Modversion|null $modversion */
        $modversion = $mod->versions()->where('version', $request->input('mod_version'))->first();

        if (! $modversion) {
            return response()->json(['error' => 'Mod version not found.'], 404);
        }

        if ($build->modversions()->where('modversion_id', $modversion->id)->exists()) {
            return response()->json(['error' => 'Mod version already in build.'], 422);
        }

        $existingModversion = $build->modversions()->whereHas('mod', function ($q) use ($mod) {
            $q->where('id', $mod->id);
        })->first();

        if ($existingModversion) {
            return response()->json(['error' => 'Another version of this mod is already in the build. Use PUT to update it.'], 422);
        }

        $build->modversions()->attach($modversion->id);

        Cache::forget('modpack:'.$slug.':build:'.$version);
        Cache::forget('modpack:'.$slug);

        return response()->json(['success' => 'Mod added to build.'], 201);
    }

    public function update(Request $request, string $slug, string $version, string $modSlug): JsonResponse
    {
        $modpack = Modpack::where('slug', $slug)->first();

        if (! $modpack) {
            return response()->json(['error' => 'Modpack not found.'], 404);
        }

        $this->authorize('update', [Build::class, $modpack]);

        /** @var Build|null $build */
        $build = $modpack->builds()->where('version', $version)->first();

        if (! $build) {
            return response()->json(['error' => 'Build not found.'], 404);
        }

        $mod = Mod::where('name', $modSlug)->first();

        if (! $mod) {
            return response()->json(['error' => 'Mod not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'mod_version' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        /** @var Modversion|null $newModversion */
        $newModversion = $mod->versions()->where('version', $request->input('mod_version'))->first();

        if (! $newModversion) {
            return response()->json(['error' => 'Mod version not found.'], 404);
        }

        /** @var Modversion|null $oldModversion */
        $oldModversion = $build->modversions()->whereHas('mod', function ($q) use ($mod) {
            $q->where('id', $mod->id);
        })->first();

        if (! $oldModversion) {
            return response()->json(['error' => 'Mod not in this build.'], 404);
        }

        $build->modversions()->detach($oldModversion->id);
        $build->modversions()->attach($newModversion->id);

        Cache::forget('modpack:'.$slug.':build:'.$version);
        Cache::forget('modpack:'.$slug);

        return response()->json(['success' => 'Mod version updated in build.']);
    }

    public function destroy(string $slug, string $version, string $modSlug): JsonResponse
    {
        $modpack = Modpack::where('slug', $slug)->first();

        if (! $modpack) {
            return response()->json(['error' => 'Modpack not found.'], 404);
        }

        $this->authorize('update', [Build::class, $modpack]);

        /** @var Build|null $build */
        $build = $modpack->builds()->where('version', $version)->first();

        if (! $build) {
            return response()->json(['error' => 'Build not found.'], 404);
        }

        $mod = Mod::where('name', $modSlug)->first();

        if (! $mod) {
            return response()->json(['error' => 'Mod not found.'], 404);
        }

        /** @var Modversion|null $modversion */
        $modversion = $build->modversions()->whereHas('mod', function ($q) use ($mod) {
            $q->where('id', $mod->id);
        })->first();

        if (! $modversion) {
            return response()->json(['error' => 'Mod not in this build.'], 404);
        }

        $build->modversions()->detach($modversion->id);

        Cache::forget('modpack:'.$slug.':build:'.$version);
        Cache::forget('modpack:'.$slug);

        return response()->json(['success' => 'Mod removed from build.']);
    }
}
