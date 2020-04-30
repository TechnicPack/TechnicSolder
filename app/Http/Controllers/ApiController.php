<?php namespace App\Http\Controllers;

use App\Build;
use App\Client;
use App\Key;
use App\Mod;
use App\Modpack;
use App\Modversion;
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

    public function getIndex()
    {
        return response()->json([
            'api' => 'TechnicSolder',
            'version' => SOLDER_VERSION,
            'stream' => SOLDER_STREAM
        ]);
    }

    public function getModpackIndex()
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

    public function getModpack($slug)
    {
        return response()->json($this->fetchModpack($slug));

    }

    public function getModpackBuild($modpackSlug, $buildName)
    {
        return response()->json($this->fetchBuild($modpackSlug, $buildName));
    }

    public function getMod($modSlug = null, $version = null)
    {
        if (empty($modSlug)) {
            // For some reason, authenticated clients or Platform (with the Platform user API key) bypass cache
            if ($this->client || $this->key) {
                $mods = Mod::all([
                    'name',
                    'pretty_name',
                ])->pluck('pretty_name', 'name');
            } else {
                $mods = Cache::remember('mods', now()->addMinutes(5), function () {
                    return Mod::all([
                        'name',
                        'pretty_name',
                    ])->pluck('pretty_name', 'name');
                });
            }

            //usort($response['mod'], function($a, $b){return strcasecmp($a['name'], $b['name']);});

            return response()->json([
                'mods' => $mods,
            ]);
        } else {
            $mod = Cache::remember('mod:' . $modSlug, now()->addMinutes(5), function () use ($modSlug) {
                return Mod::with('versions')->where('name', $modSlug)->first();
            });

            if (!$mod) {
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

            if (!$modVersion) {
                return response()->json(["error" => "Mod version does not exist"]);
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

    public function getVerify($key = null)
    {
        if (!$key) {
            return response()->json(["error" => "No API key provided."]);
        }

        $key = Key::where('api_key', $key)->first();

        if (!$key) {
            return response()->json(["error" => "Invalid key provided."]);
        }

        return response()->json([
            "valid" => "Key validated.",
            "name" => $key->name,
            "created_at" => $key->created_at,
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
        if (!$this->key) {
            // If a key isn't specified, we filter modpacks
            $modpacks = $modpacks->filter(function ($modpack) {
                // Allow non-private, non-hidden modpacks
                if ($modpack->private == 0 && $modpack->hidden == 0) return true;

                // Reject if this is a private or hidden modpack, and a client isn't set
                if (!$this->client) return false;

                // Allow if the current client has access to this modpack
                return $this->client->modpacks->contains($modpack);
            });
        }

        return $modpacks;
    }

    private function fetchModpack($slug)
    {
        $response = [];

        if (Cache::has('modpack.' . $slug) && empty($this->client) && empty($this->key)) {
            $modpack = Cache::get('modpack.' . $slug);
        } else {
            $modpack = Modpack::with('Builds')
                ->where("slug", "=", $slug)->first();
            if (empty($this->client) && empty($this->key)) {
                Cache::put('modpack.' . $slug, $modpack, now()->addMinutes(5));
            }
        }

        if (empty($modpack)) {
            return ["error" => "Modpack does not exist"];
        }

        $response['id'] = $modpack->id;
        $response['name'] = $modpack->slug;
        $response['display_name'] = $modpack->name;
        $response['url'] = $modpack->url;
        $response['icon'] = $modpack->icon_url;
        $response['icon_md5'] = $modpack->icon_md5;
        $response['logo'] = $modpack->logo_url;
        $response['logo_md5'] = $modpack->logo_md5;
        $response['background'] = $modpack->background_url;
        $response['background_md5'] = $modpack->background_md5;
        $response['recommended'] = $modpack->recommended;
        $response['latest'] = $modpack->latest;
        $response['builds'] = [];

        foreach ($modpack->builds as $build) {
            if ($build->is_published) {
                if (!$build->private || isset($this->key)) {
                    array_push($response['builds'], $build->version);
                } else {
                    if (isset($this->client)) {
                        foreach ($this->client->modpacks as $pmodpack) {
                            if ($modpack->id == $pmodpack->id) {
                                array_push($response['builds'], $build->version);
                            }
                        }
                    }
                }
            }
        }

        return $response;
    }

    private function fetchBuild($slug, $build)
    {
        $response = [];

        if (Cache::has('modpack.' . $slug) && empty($this->client) && empty($this->key)) {
            $modpack = Cache::Get('modpack.' . $slug);
        } else {
            $modpack = Modpack::where("slug", "=", $slug)->first();
            if (empty($this->client) && empty($this->key)) {
                Cache::put('modpack.' . $slug, $modpack, now()->addMinutes(5));
            }
        }

        if (empty($modpack)) {
            return ["error" => "Modpack does not exist"];
        }

        $buildpass = $build;
        if (Cache::has('modpack.' . $slug . '.build.' . $build) && empty($this->client) && empty($this->key)) {
            $build = Cache::get('modpack.' . $slug . '.build.' . $build);
        } else {
            $build = Build::with('Modversions')
                ->where("modpack_id", "=", $modpack->id)
                ->where("version", "=", $build)->first();
            if (empty($this->client) && empty($this->key)) {
                Cache::put('modpack.' . $slug . '.build.' . $buildpass, $build, now()->addMinutes(5));
            }
        }

        if (empty($build)) {
            return ["error" => "Build does not exist"];
        }

        $response['id'] = $build->id;
        $response['minecraft'] = $build->minecraft;
        $response['java'] = $build->min_java;
        $response['memory'] = $build->min_memory;
        $response['forge'] = $build->forge;
        $response['mods'] = [];

        if (!Request::has('include')) {
            if (Cache::has('modpack.' . $slug . '.build.' . $buildpass . 'modversion') && empty($this->client) && empty($this->key)) {
                $response['mods'] = Cache::get('modpack.' . $slug . '.build.' . $buildpass . 'modversion');
            } else {
                foreach ($build->modversions as $modversion) {
                    $response['mods'][] = [
                        "id" => $modversion->id,
                        "name" => $modversion->mod->name,
                        "version" => $modversion->version,
                        "md5" => $modversion->md5,
                        "filesize" => $modversion->filesize,
                        "url" => config('solder.mirror_url') . 'mods/' . $modversion->mod->name . '/' . $modversion->mod->name . '-' . $modversion->version . '.zip'
                    ];
                }
                usort($response['mods'], function ($a, $b) {
                    return strcasecmp($a['name'], $b['name']);
                });
                Cache::put('modpack.' . $slug . '.build.' . $buildpass . 'modversion', $response['mods'], now()->addMinutes(5));
            }
        } else {
            if (Request::input('include') == "mods") {
                if (Cache::has('modpack.' . $slug . '.build.' . $buildpass . 'modversion.include.mods') && empty($this->client) && empty($this->key)) {
                    $response['mods'] = Cache::get('modpack.' . $slug . '.build.' . $buildpass . 'modversion.include.mods');
                } else {
                    foreach ($build->modversions as $modversion) {
                        $response['mods'][] = [
                            "id" => $modversion->id,
                            "name" => $modversion->mod->name,
                            "version" => $modversion->version,
                            "md5" => $modversion->md5,
                            "filesize" => $modversion->filesize,
                            "pretty_name" => $modversion->mod->pretty_name,
                            "author" => $modversion->mod->author,
                            "description" => $modversion->mod->description,
                            "link" => $modversion->mod->link,
                            "url" => config('solder.mirror_url') . 'mods/' . $modversion->mod->name . '/' . $modversion->mod->name . '-' . $modversion->version . '.zip'
                        ];
                    }
                    usort($response['mods'], function ($a, $b) {
                        return strcasecmp($a['name'], $b['name']);
                    });
                    Cache::put('modpack.' . $slug . '.build.' . $buildpass . 'modversion.include.mods', $response['mods'], now()->addMinutes(5));
                }
            } else {
                $request = explode(",", Request::input('include'));
                if (Cache::has('modpack.' . $slug . '.build.' . $buildpass . 'modversion.include.' . $request) && empty($this->client) && empty($this->key)) {
                    $response['mods'] = Cache::get('modpack.' . $slug . '.build.' . $buildpass . 'modversion.include.' . $request);
                } else {
                    foreach ($build->modversions as $modversion) {
                        $data = [
                            "id" => $modversion->id,
                            "name" => $modversion->mod->name,
                            "version" => $modversion->version,
                            "md5" => $modversion->md5,
                            "filesize" => $modversion->filesize,
                        ];
                        $mod = (array) $modversion->mod;
                        $mod = $mod['attributes'];
                        foreach ($request as $type) {
                            if (isset($mod[$type])) {
                                $data[$type] = $mod[$type];
                            }
                        }

                        $response['mods'][] = $data;
                    }
                    usort($response['mods'], function ($a, $b) {
                        return strcasecmp($a['name'], $b['name']);
                    });
                    Cache::put('modpack.' . $slug . '.build.' . $buildpass . 'modversion.include.' . $request, $response['mods'],
                        now()->addMinutes(5));
                }
            }
        }

        return $response;
    }

}