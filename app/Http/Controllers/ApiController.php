<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Client;
use App\Models\Key;
use App\Models\Mod;
use App\Models\Modpack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class ApiController extends Controller
{
    /**
     * @var Client|null The Solder client associated with the request
     */
    private $client;

    /**
     * @var Key|null The Platform API key associated with the request
     */
    private $key;

    public function __construct()
    {
        /* This checks the client list for the CID. If a matching CID is found, all caching will be ignored
           for this request */

        $clients = Cache::remember('clients', now()->addMinutes(1), function () {
            return Client::all();
        });

        $keys = Cache::remember('keys', now()->addMinutes(1), function () {
            return Key::all();
        });

        $inputClientId = Request::input('cid');
        if ($inputClientId) {
            $this->client = $clients->firstWhere('uuid', '===', $inputClientId);
        } else {
            $this->client = null;
        }

        $inputKey = Request::input('k');
        if ($inputKey) {
            $this->key = $keys->firstWhere('api_key', '===', $inputKey);
        } else {
            $this->key = null;
        }
    }

    public function getIndex(): JsonResponse
    {
        return response()->json([
            'api' => 'TechnicSolder',
            'version' => SOLDER_VERSION,
            'stream' => SOLDER_STREAM,
        ]);
    }

    public function getModpackIndex(): JsonResponse
    {
        $includeFull = Request::input('include') === 'full';

        $modpacks = $this->fetchModpacks();

        $response = [];

        if ($includeFull) {
            $modpacks->load('builds');

            $response['modpacks'] = [];

            foreach ($modpacks as $modpack) {
                $response['modpacks'][$modpack->slug] = $this->fetchModpack($modpack->slug);
            }
        } else {
            $response['modpacks'] = $modpacks->pluck('name', 'slug');
        }

        $response['mirror_url'] = config('solder.mirror_url');

        return response()->json($response);
    }

    public function getModpack($slug): JsonResponse
    {
        $response = $this->fetchModpack($slug);

        if (! $response) {
            return response()->json(['error' => 'Modpack does not exist'], 404);
        }

        return response()->json($response);
    }

    public function getModpackBuild($modpackSlug, $buildName): JsonResponse
    {
        return response()->json($this->fetchBuild($modpackSlug, $buildName));
    }

    public function getMod($modSlug = null, $version = null)
    {
        if (config('solder.disable_mod_api')) {
            return response()->json(['error' => 'Mod API has been disabled'], 404);
        }

        if (empty($modSlug)) {
            $mods = Cache::remember('mods', now()->addMinutes(5), function () {
                return Mod::pluck('pretty_name', 'name');
            });

            //usort($response['mod'], function($a, $b){return strcasecmp($a['name'], $b['name']);});

            return response()->json([
                'mods' => $mods,
            ]);
        } else {
            $mod = Cache::remember('mod:'.$modSlug, now()->addMinutes(5), function () use ($modSlug) {
                return Mod::with('versions')->where('name', $modSlug)->first();
            });

            if (! $mod) {
                return response()->json(['error' => 'Mod does not exist'], 404);
            }

            if (empty($version)) {
                $response = $mod->only([
                    'id',
                    'name',
                    'pretty_name',
                    'author',
                    'description',
                    'link',
                ]);

                $response['versions'] = $mod->versions->pluck('version');

                return response()->json($response);
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
    }

    public function getVerify($key = null): JsonResponse
    {
        if (! $key) {
            return response()->json(['error' => 'No API key provided.'], 400);
        }

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

    /* Private Functions */

    private function fetchModpacks()
    {
        $modpacks = Cache::remember('modpacks', now()->addMinutes(5), function () {
            return Modpack::all();
        });

        if ($this->client) {
            // Eager load client-specific modpacks
            $this->client->load('modpacks');
        }

        // Requests authenticated with a Platform key have access to all modpacks
        if (! $this->key) {
            // If a key isn't specified, we filter modpacks
            $modpacks = $modpacks->filter(function ($modpack) {
                // Allow non-private, non-hidden modpacks
                if ($modpack->private == 0 && $modpack->hidden == 0) {
                    return true;
                }

                // Reject if this is a private or hidden modpack, and a client isn't set
                if (! $this->client) {
                    return false;
                }

                // Allow if the current client has access to this modpack
                return $this->client->modpacks->contains($modpack);
            });
        }

        return $modpacks;
    }

    private function fetchModpack($slug)
    {
        $modpack = Cache::remember('modpack:'.$slug, now()->addMinutes(5), function () use ($slug) {
            return Modpack::with('builds')
                ->where('slug', $slug)
                ->first();
        });

        if (! $modpack) {
            return null;
        }

        return $modpack->toApiResponse($this->client, $this->key);
    }

    private function fetchBuild($modpackSlug, $buildName)
    {
        $modpack = Cache::remember('modpack:'.$modpackSlug, now()->addMinutes(5), function () use ($modpackSlug) {
            return Modpack::with('builds')
                ->where('slug', $modpackSlug)
                ->first();
        });

        if (! $modpack) {
            return ['error' => 'Modpack does not exist'];
        }

        $build = Cache::remember('modpack:'.$modpackSlug.':build:'.$buildName,
            now()->addMinutes(5),
            function () use ($modpack, $buildName) {
                $build = $modpack->builds->firstWhere('version', '===', $buildName);

                $build->load(['modversions', 'modversions.mod']);

                return $build;
            });

        if (! $build) {
            return ['error' => 'Build does not exist'];
        }

        if (! $build->is_published || $build->private && ! ($this->key || ($this->client && $this->client->modpacks->contains($modpack)))) {
            return ['error' => 'You are not authorized to view this build'];
        }

        $response = [
            'id' => $build->id,
            'minecraft' => $build->minecraft,
            'java' => $build->min_java,
            'memory' => $build->min_memory,
            'forge' => $build->forge,
        ];

        $includeFullMods = Request::input('include') === 'mods';

        $mods = $build->modversions->map(function ($modversion) use ($includeFullMods) {
            return $modversion->toApiResponse($includeFullMods);
        })->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();

        $response['mods'] = $mods;

        return $response;
    }
}
