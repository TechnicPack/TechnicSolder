<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiAuthContext;
use App\Http\Controllers\Controller;
use App\Models\Modpack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Validator;

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
