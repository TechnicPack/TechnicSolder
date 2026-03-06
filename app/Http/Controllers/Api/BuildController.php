<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiAuthContext;
use App\Http\Controllers\Controller;
use App\Models\Build;
use App\Models\Modpack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Validator;

class BuildController extends Controller
{
    public function show(string $slug, string $version): JsonResponse
    {
        $auth = ApiAuthContext::fromRequest();

        $modpack = Cache::remember('modpack:'.$slug, now()->addMinutes(5), function () use ($slug) {
            return Modpack::with('builds')->where('slug', $slug)->first();
        });

        if (! $modpack) {
            return response()->json(['error' => 'Modpack does not exist'], 404);
        }

        if (($modpack->private || $modpack->hidden) && ! $modpack->isAccessibleBy($auth)) {
            return response()->json(['error' => 'Modpack does not exist'], 404);
        }

        $buildCacheKey = 'modpack:'.$slug.':build:'.$version;

        $build = Cache::get($buildCacheKey);

        if (! $build) {
            $build = $modpack->builds->firstWhere('version', '===', $version);

            if ($build) {
                $build->load(['modversions', 'modversions.mod']);
                Cache::put($buildCacheKey, $build, now()->addMinutes(5));
            } else {
                $build = Build::NOT_FOUND_CACHE_VALUE;
                Cache::put($buildCacheKey, $build, now()->addMinute());
            }
        }

        if ($build === Build::NOT_FOUND_CACHE_VALUE) {
            return response()->json(['error' => 'Build does not exist'], 404);
        }

        if (! $build->is_published || ($build->private && ! $modpack->isAccessibleBy($auth))) {
            return response()->json(['error' => 'Build does not exist'], 404);
        }

        $response = [
            'id' => $build->id,
            'minecraft' => $build->minecraft,
            'java' => $build->min_java,
            'memory' => $build->min_memory,
            'forge' => $build->forge,
        ];

        $includeFullMods = RequestFacade::input('include') === 'mods';

        $mods = $build->modversions->map(function ($modversion) use ($includeFullMods) {
            return $modversion->toApiResponse($includeFullMods);
        })->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();

        $response['mods'] = $mods;

        return response()->json($response);
    }

    public function store(Request $request, string $slug): JsonResponse
    {
        $modpack = Modpack::where('slug', $slug)->first();

        if (! $modpack) {
            return response()->json(['error' => 'Modpack not found.'], 404);
        }

        $this->authorize('create', [Build::class, $modpack]);

        $validator = Validator::make($request->all(), [
            'version' => 'required',
            'minecraft' => 'required',
            'clone_from' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if ($modpack->builds()->where('version', $request->input('version'))->exists()) {
            return response()->json(['error' => 'Build version already exists for this modpack.'], 422);
        }

        /** @var Build $build */
        $build = $modpack->builds()->create($request->only([
            'version', 'minecraft', 'forge', 'is_published', 'private', 'min_java', 'min_memory',
        ]));

        if ($request->filled('clone_from')) {
            /** @var Build|null $source */
            $source = $modpack->builds()->where('version', $request->input('clone_from'))->first();
            if ($source) {
                $build->modversions()->sync($source->modversions->pluck('id'));
            }
        }

        Cache::forget('modpack:'.$slug);

        return response()->json($build, 201);
    }

    public function update(Request $request, string $slug, string $version): JsonResponse
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

        $build->update($request->only([
            'version', 'minecraft', 'forge', 'is_published', 'private', 'min_java', 'min_memory',
        ]));

        Cache::forget('modpack:'.$slug);
        Cache::forget('modpack:'.$slug.':build:'.$version);
        if ($version !== $build->version) {
            Cache::forget('modpack:'.$slug.':build:'.$build->version);
        }

        return response()->json($build);
    }

    public function destroy(string $slug, string $version): JsonResponse
    {
        $modpack = Modpack::where('slug', $slug)->first();

        if (! $modpack) {
            return response()->json(['error' => 'Modpack not found.'], 404);
        }

        $this->authorize('delete', [Build::class, $modpack]);

        /** @var Build|null $build */
        $build = $modpack->builds()->where('version', $version)->first();

        if (! $build) {
            return response()->json(['error' => 'Build not found.'], 404);
        }

        $build->modversions()->detach();
        $build->delete();

        Cache::forget('modpack:'.$slug);
        Cache::forget('modpack:'.$slug.':build:'.$version);

        return response()->json(['success' => 'Build deleted.']);
    }
}
