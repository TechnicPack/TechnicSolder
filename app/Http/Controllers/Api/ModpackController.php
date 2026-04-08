<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiAuthContext;
use App\Http\Controllers\Controller;
use App\Models\Build;
use App\Models\Modpack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ModpackController extends Controller
{
    public function index(): JsonResponse
    {
        $auth = ApiAuthContext::fromRequest();
        $includeFull = RequestFacade::input('include') === 'full';

        $modpacks = $this->fetchModpacks($auth);

        $response = [];

        if ($includeFull) {
            $modpacks->load('builds');

            $response['modpacks'] = [];

            foreach ($modpacks as $modpack) {
                $response['modpacks'][$modpack->slug] = $this->fetchModpack($modpack->slug, $auth);
            }
        } else {
            $response['modpacks'] = $modpacks->pluck('name', 'slug');
        }

        $response['mirror_url'] = config('solder.mirror_url');

        return response()->json($response);
    }

    public function show(string $slug): JsonResponse
    {
        $auth = ApiAuthContext::fromRequest();
        $response = $this->fetchModpack($slug, $auth);

        if (! $response) {
            return response()->json(['error' => 'Modpack does not exist'], 404);
        }

        return response()->json($response);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Modpack::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:modpacks',
            'slug' => 'required|unique:modpacks|alpha_dash',
            'url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $modpack = Modpack::create($request->only([
            'name', 'slug', 'hidden', 'private', 'url', 'order',
        ]));

        Cache::forget('modpacks');
        Cache::forget('allmodpacks');

        return response()->json($modpack, 201);
    }

    public function clone(Request $request, string $slug): JsonResponse
    {
        $modpack = Modpack::with('builds.modversions')->where('slug', $slug)->first();

        if (! $modpack) {
            return response()->json(['error' => 'Modpack not found.'], 404);
        }

        $this->authorize('create', Modpack::class);
        $this->authorize('update', $modpack);

        $slug = Str::slug($request->input('slug'));

        $validator = Validator::make(
            array_merge($request->all(), ['slug' => $slug]),
            [
                'name' => 'required|unique:modpacks',
                'slug' => 'required|unique:modpacks|alpha_dash',
            ],
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $newModpack = DB::transaction(function () use ($modpack, $request, $slug) {
            $newModpack = new Modpack;
            $newModpack->name = $request->input('name');
            $newModpack->slug = $slug;
            $newModpack->hidden = $request->boolean('hidden');
            $newModpack->private = $modpack->private;
            $newModpack->recommended = $modpack->recommended;
            $newModpack->latest = $modpack->latest;
            $newModpack->icon = $modpack->icon;
            $newModpack->icon_md5 = $modpack->icon_md5;
            $newModpack->icon_url = $modpack->icon_url;
            $newModpack->logo = $modpack->logo;
            $newModpack->logo_md5 = $modpack->logo_md5;
            $newModpack->logo_url = $modpack->logo_url;
            $newModpack->background = $modpack->background;
            $newModpack->background_md5 = $modpack->background_md5;
            $newModpack->background_url = $modpack->background_url;
            $newModpack->save();

            foreach ($modpack->builds as $build) {
                $newBuild = new Build;
                $newBuild->modpack_id = $newModpack->id;
                $newBuild->version = $build->version;
                $newBuild->minecraft = $build->minecraft;
                $newBuild->forge = $build->forge;
                $newBuild->is_published = $build->is_published;
                $newBuild->private = $build->private;
                $newBuild->min_java = $build->min_java;
                $newBuild->min_memory = $build->min_memory;
                $newBuild->save();

                $versionIds = $build->modversions->pluck('id')->toArray();
                $newBuild->modversions()->sync($versionIds);
            }

            return $newModpack;
        });

        $request->user()->permission->grantModpackAccess($newModpack->id);

        Cache::forget('modpacks');
        Cache::forget('allmodpacks');

        return response()->json($newModpack->load('builds'), 201);
    }

    public function update(Request $request, string $slug): JsonResponse
    {
        $modpack = Modpack::where('slug', $slug)->first();

        if (! $modpack) {
            return response()->json(['error' => 'Modpack not found.'], 404);
        }

        $this->authorize('update', $modpack);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|unique:modpacks,name,'.$modpack->id,
            'slug' => 'sometimes|required|alpha_dash|unique:modpacks,slug,'.$modpack->id,
            'url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $oldSlug = $modpack->slug;
        $modpack->update($request->only([
            'name', 'slug', 'hidden', 'private', 'url', 'order',
            'recommended', 'latest',
        ]));

        Cache::forget('modpack:'.$oldSlug);
        if ($oldSlug !== $modpack->slug) {
            Cache::forget('modpack:'.$modpack->slug);
        }
        Cache::forget('modpacks');
        Cache::forget('allmodpacks');

        return response()->json($modpack);
    }

    public function destroy(string $slug): JsonResponse
    {
        $modpack = Modpack::where('slug', $slug)->first();

        if (! $modpack) {
            return response()->json(['error' => 'Modpack not found.'], 404);
        }

        $this->authorize('delete', $modpack);

        foreach ($modpack->builds as $build) {
            $build->modversions()->detach();
            Cache::forget('modpack:'.$slug.':build:'.$build->version);
            $build->delete();
        }

        $modpack->clients()->detach();
        $modpack->delete();

        Cache::forget('modpack:'.$slug);
        Cache::forget('modpacks');
        Cache::forget('allmodpacks');
        Cache::forget('clients');

        return response()->json(['success' => 'Modpack deleted.']);
    }

    private function fetchModpacks(ApiAuthContext $auth)
    {
        $modpacks = Cache::remember('modpacks', now()->addMinutes(5), function () {
            return Modpack::all();
        });

        return $modpacks->filter(function ($modpack) use ($auth) {
            if ($modpack->private == 0 && $modpack->hidden == 0) {
                return true;
            }

            return $modpack->isAccessibleBy($auth);
        });
    }

    private function fetchModpack(string $slug, ApiAuthContext $auth)
    {
        $modpack = Cache::remember('modpack:'.$slug, now()->addMinutes(5), function () use ($slug) {
            return Modpack::with('builds')
                ->where('slug', $slug)
                ->first();
        });

        if (! $modpack) {
            return null;
        }

        if (($modpack->private || $modpack->hidden) && ! $modpack->isAccessibleBy($auth)) {
            return null;
        }

        return $modpack->toApiResponse($auth);
    }
}
