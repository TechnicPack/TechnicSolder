<?php

namespace App\Http\Controllers;

use App\JavaVersionsEnum;
use App\Libraries\MinecraftUtils;
use App\Models\Build;
use App\Models\Client;
use App\Models\Mod;
use App\Models\Modpack;
use App\Models\Modversion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ModpackController extends Controller
{
    public function getIndex(): RedirectResponse
    {
        return redirect('modpack/list');
    }

    public function getList(): View
    {
        $this->authorize('viewAny', Modpack::class);

        $modpacks = Modpack::all();

        $user = Auth::user();
        $perms = $user->permission;
        if (! $perms->solder_full) {
            $modpacks = $modpacks->filter(fn ($modpack) => $perms->canAccessModpack($modpack->id));
        }

        return view('modpack.list')->with('modpacks', $modpacks);
    }

    public function getView($modpack_id = null)
    {
        $modpack = Modpack::with([
            'builds' => function ($query) {
                $query->withCount('modversions');
            },
        ])
            ->find($modpack_id);

        if (empty($modpack)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('update', $modpack);

        return view('modpack.view')->with('modpack', $modpack);
    }

    public function getBuild($build_id = null)
    {
        $build = Build::with('modpack')
            ->with('modversions')
            ->with('modversions.mod')
            ->with('modversions.mod.versions')
            ->find($build_id);
        if (empty($build)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('update', [Build::class, $build->modpack]);

        $mods = Mod::all();

        return view('modpack.build.view')
            ->with('build', $build)
            ->with('mods', $mods);
    }

    public function getExportBuild($build_id = null): StreamedResponse|RedirectResponse
    {
        $build = Build::with('modpack', 'modversions.mod')
            ->find($build_id);

        if (empty($build)) {
            return redirect('modpack/list')->withErrors(['Build not found']);
        }

        $this->authorize('update', [Build::class, $build->modpack]);

        $filename = $build->modpack->slug.'_'.$build->version.'.csv';

        $sortedModversions = $build->modversions
            ->sortBy(fn (Modversion $modversion) => strtolower($modversion->mod->pretty_name ?: $modversion->mod->name));

        return response()->streamDownload(function () use ($sortedModversions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['mod_name', 'mod_slug', 'version', 'md5', 'filesize']);

            foreach ($sortedModversions as $modversion) {
                fputcsv($handle, [
                    $modversion->mod->pretty_name ?: $modversion->mod->name,
                    $modversion->mod->name,
                    $modversion->version,
                    $modversion->md5,
                    $modversion->filesize,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function getEditBuild($build_id = null)
    {
        $build = Build::with('modpack')->find($build_id);
        if (empty($build)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('update', [Build::class, $build->modpack]);

        $minecraft = MinecraftUtils::getMinecraftVersions();

        return view('modpack.build.edit')->with('build', $build)->with('minecraft', $minecraft);
    }

    public function postEditBuild($build_id = null)
    {
        $build = Build::with('modpack')->find($build_id);
        if (empty($build)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('update', [Build::class, $build->modpack]);

        $rules = [
            'version' => 'required',
            'minecraft' => 'required',
            'memory' => [
                'required_if_accepted:memory-enabled',
                'numeric',
            ],
            'java-version' => [
                'nullable',
                Rule::enum(JavaVersionsEnum::class),
            ],
        ];

        $messages = [
            'memory.numeric' => 'You may enter in numbers only for the memory requirement',
        ];

        $attributes = [
            'version' => 'modpack version',
            'java-version' => 'Java version',
        ];

        $validation = Validator::make(Request::all(), $rules, $messages, $attributes);
        if ($validation->fails()) {
            return redirect('modpack/build/'.$build->id.'/edit')
                ->withErrors($validation->messages())
                ->withInput();
        }

        // Wrap changes inside a transaction so potential modpack changes also get rolled back if anything fails
        DB::transaction(function () use ($build) {
            $oldVersion = $build->version;

            $build->version = Request::input('version');

            $minecraft = Request::input('minecraft');

            $build->minecraft = $minecraft;
            $build->min_java = Request::input('java-version');
            $build->min_memory = Request::input('memory-enabled') ? Request::input('memory') : 0;
            $build->save();

            // If the build's name/version changes then we need to check the modpack's latest/recommended build
            if ($oldVersion !== $build->version) {
                // Update the modpack's latest build if this was it
                if ($build->modpack->latest === $oldVersion) {
                    $build->modpack->latest = $build->version;
                    $build->modpack->save();
                }

                // Update the modpack's recommended build if this was it
                if ($build->modpack->recommended === $oldVersion) {
                    $build->modpack->recommended = $build->version;
                    $build->modpack->save();
                }
            }
        });

        Cache::forget('modpack:'.$build->modpack->slug);
        Cache::forget('modpack:'.$build->modpack->slug.':build:'.$build->version);

        return redirect('modpack/build/'.$build->id);
    }

    public function getDeleteBuild($build_id = null)
    {
        $build = Build::with('modpack')->find($build_id);
        if (empty($build)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('delete', [Build::class, $build->modpack]);

        return view('modpack.build.delete')->with('build', $build);
    }

    public function postDeleteBuild($build_id = null)
    {
        $build = Build::with('modpack')->find($build_id);
        if (empty($build)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('delete', [Build::class, $build->modpack]);

        $switchrec = 0;
        $switchlat = 0;
        $modpack = $build->modpack;
        if ($build->version == $modpack->recommended) {
            $switchrec = 1;
        }
        if ($build->version == $modpack->latest) {
            $switchlat = 1;
        }
        $buildVersion = $build->version;
        $build->modversions()->sync([]);
        $build->delete();
        if ($switchrec) {
            $recbuild = Build::where('modpack_id', '=', $modpack->id)
                ->orderBy('id', 'desc')->first();
            $modpack->recommended = $recbuild?->version;
        }

        if ($switchlat) {
            $latbuild = Build::where('modpack_id', '=', $modpack->id)
                ->orderBy('id', 'desc')->first();
            $modpack->latest = $latbuild?->version;
        }
        $modpack->save();
        Cache::forget('modpack:'.$modpack->slug);
        Cache::forget('modpack:'.$modpack->slug.':build:'.$buildVersion);

        return redirect('modpack/view/'.$modpack->id)->with('success', 'Build deleted.');
    }

    public function getAddBuild($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('create', [Build::class, $modpack]);

        $minecraft = MinecraftUtils::getMinecraftVersions();

        $user = Auth::user();
        $cloneableModpacks = Modpack::with(['builds' => fn ($q) => $q->orderBy('id', 'desc')])->orderBy('name')->get();
        if (! $user->permission->solder_full) {
            $cloneableModpacks = $cloneableModpacks->filter(fn ($mp) => $user->permission->canAccessModpack($mp->id));
        }
        $cloneableModpacks = $cloneableModpacks->sortBy(fn ($mp) => $mp->id === $modpack->id ? 0 : 1);

        return view('modpack.build.create')
            ->with([
                'modpack' => $modpack,
                'minecraft' => $minecraft,
                'cloneableModpacks' => $cloneableModpacks,
            ]);
    }

    public function postAddBuild($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('create', [Build::class, $modpack]);

        $rules = [
            'version' => [
                'required',
                Rule::unique('builds')->where(function ($query) use ($modpack) {
                    return $query->where('modpack_id', $modpack->id);
                }),
            ],
            'minecraft' => 'required',
            'memory' => [
                'required_if_accepted:memory-enabled',
                'numeric',
            ],
            'java-version' => [
                'nullable',
                Rule::enum(JavaVersionsEnum::class),
            ],
        ];

        $messages = [
            'memory.numeric' => 'You may enter in numbers only for the memory requirement',
        ];
        $attributes = [
            'version' => 'modpack version',
            'java-version' => 'Java version',
        ];

        $validation = Validator::make(Request::all(), $rules, $messages, $attributes);
        if ($validation->fails()) {
            return redirect('modpack/add-build/'.$modpack_id)->withErrors($validation->messages())->withInput();
        }

        $clone = Request::input('clone');
        $build = new Build;
        $build->modpack_id = $modpack->id;
        $build->version = Request::input('version');

        $minecraft = Request::input('minecraft');

        $build->minecraft = $minecraft;
        $build->min_java = Request::input('java-version');
        $build->min_memory = Request::input('memory-enabled') ? Request::input('memory') : 0;
        $build->save();
        Cache::forget('modpack:'.$modpack->slug);
        if (! empty($clone)) {
            $clone_build = Build::with('modpack')->find($clone);
            if (! $clone_build) {
                return redirect('modpack/build/'.$build->id)
                    ->withErrors(['Clone source build not found.']);
            }
            if (! Auth::user()->permission->canAccessModpack($clone_build->modpack_id)) {
                return redirect('modpack/build/'.$build->id)
                    ->withErrors(['You do not have permission to clone from that modpack.']);
            }
            $version_ids = $clone_build->modversions()->pluck('modversions.id')->toArray();
            $build->modversions()->sync($version_ids);
        }

        return redirect('modpack/build/'.$build->id);
    }

    public function getCreate(): View
    {
        $this->authorize('create', Modpack::class);

        return view('modpack.create');
    }

    public function postCreate(): RedirectResponse
    {
        $this->authorize('create', Modpack::class);

        $rules = [
            'name' => 'required|unique:modpacks',
            'slug' => 'required|unique:modpacks',
            'hidden' => 'sometimes|required',
        ];

        $messages = [
            'name_required' => 'You must enter a modpack name.',
            'slug_required' => 'You must enter a modpack slug',
        ];

        $validation = Validator::make(Request::all(), $rules, $messages);

        if ($validation->fails()) {
            return redirect('modpack/create')->withErrors($validation->messages());
        }

        $modpack = new Modpack;
        $modpack->name = Request::input('name');
        $modpack->slug = Str::slug(Request::input('slug'));
        $modpack->hidden = request()->boolean('hidden');
        $modpack->icon_md5 = null;
        $modpack->icon_url = URL::asset('/resources/default/icon.png');
        $modpack->logo_md5 = null;
        $modpack->logo_url = URL::asset('/resources/default/logo.png');
        $modpack->background_md5 = null;
        $modpack->background_url = URL::asset('/resources/default/background.jpg');
        $modpack->save();
        Cache::forget('modpacks');
        Cache::forget('allmodpacks');

        Auth::user()->permission->grantModpackAccess($modpack->id);

        return redirect('modpack/view/'.$modpack->id);
    }

    public function getClone($modpack_id)
    {
        $modpack = Modpack::with('builds')->find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('create', Modpack::class);
        $this->authorize('update', $modpack);

        return view('modpack.clone')->with('modpack', $modpack);
    }

    public function postClone($modpack_id): RedirectResponse
    {
        $modpack = Modpack::with('builds.modversions')->find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list')->withErrors(['Modpack not found']);
        }

        $this->authorize('create', Modpack::class);
        $this->authorize('update', $modpack);

        $slug = Str::slug(Request::input('slug'));

        $rules = [
            'name' => 'required|unique:modpacks',
            'slug' => 'required|unique:modpacks|alpha_dash',
            'hidden' => 'sometimes|required',
        ];

        $messages = [
            'name_required' => 'You must enter a modpack name.',
            'slug_required' => 'You must enter a modpack slug',
        ];

        $validation = Validator::make(
            array_merge(Request::all(), ['slug' => $slug]),
            $rules,
            $messages,
        );

        if ($validation->fails()) {
            return redirect('modpack/clone/'.$modpack_id)->withErrors($validation->messages());
        }

        $newModpack = DB::transaction(function () use ($modpack, $slug) {
            $newModpack = new Modpack;
            $newModpack->name = Request::input('name');
            $newModpack->slug = $slug;
            $newModpack->hidden = request()->boolean('hidden');
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

        Auth::user()->permission->grantModpackAccess($newModpack->id);

        Cache::forget('modpacks');
        Cache::forget('allmodpacks');

        return redirect('modpack/view/'.$newModpack->id);
    }

    public function getEdit($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('dashboard')->withErrors(['Modpack not found']);
        }

        $this->authorize('update', $modpack);

        $currentClients = [];
        foreach ($modpack->clients as $client) {
            array_push($currentClients, $client->id);
        }

        $allClients = Client::all();

        return view('modpack.edit')
            ->with('modpack', $modpack)
            ->with('currentClients', $currentClients)
            ->with('allClients', $allClients);
    }

    public function postEdit($modpack_id): RedirectResponse
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list/')->withErrors(['Modpack not found']);
        }

        $this->authorize('update', $modpack);

        $rules = [
            'name' => 'required|unique:modpacks,name,'.$modpack->id,
            'slug' => 'required|unique:modpacks,slug,'.$modpack->id,
        ];

        $messages = [
            'name_required' => 'You must enter a modpack name.',
            'slug_required' => 'You must enter a modpack slug',
        ];

        $validation = Validator::make(Request::all(), $rules, $messages);
        if ($validation->fails()) {
            return redirect('modpack/edit/'.$modpack_id)->withErrors($validation->messages());
        }

        $oldSlug = $modpack->slug;
        $modpack->name = Request::input('name');
        $modpack->slug = Request::input('slug');
        $modpack->hidden = Request::boolean('hidden');
        $modpack->private = Request::boolean('private');
        $modpack->save();

        Cache::forget('modpack:'.$oldSlug);
        if ($oldSlug !== $modpack->slug) {
            Cache::forget('modpack:'.$modpack->slug);
        }
        Cache::forget('modpacks');
        Cache::forget('allmodpacks');

        /* Client Syncing */
        $clients = Request::input('clients');
        if ($clients) {
            $clients = array_filter(array_map('intval', (array) $clients));
            $validClients = Client::whereIn('id', $clients)->pluck('id')->all();
            $modpack->clients()->sync($validClients);
        } else {
            $modpack->clients()->sync([]);
        }

        return redirect('modpack/edit/'.$modpack->id)->with('success', 'Modpack saved');
    }

    public function getDelete($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list/')->withErrors(['Modpack not found']);
        }

        $this->authorize('delete', $modpack);

        return view('modpack.delete')->with(['modpack' => $modpack]);
    }

    public function postDelete($modpack_id): RedirectResponse
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list/')->withErrors(['Modpack not found']);
        }

        $this->authorize('delete', $modpack);

        foreach ($modpack->builds as $build) {
            $build->modversions()->sync([]);
            Cache::forget('modpack:'.$modpack->slug.':build:'.$build->version);
            $build->delete();
        }

        $modpack->clients()->sync([]);
        $modpack->delete();
        Cache::forget('modpack:'.$modpack->slug);
        Cache::forget('modpacks');
        Cache::forget('allmodpacks');
        Cache::forget('clients');

        return redirect('modpack/list/')->with('success', 'Modpack Deleted');
    }

    /**
     * AJAX Methods for Modpack Manager
     **/
    public function anyModify($action = null): JsonResponse
    {
        if (! Request::ajax()) {
            abort(404);
        }

        if (empty($action)) {
            abort(400);
        }

        switch ($action) {
            case 'version': // Change mod version in a build
                $version_id = Request::input('version');
                $modversion_id = Request::input('modversion_id');
                $build = Build::with('modpack')->findOrFail(Request::input('build_id'));
                $this->authorize('update', [Build::class, $build->modpack]);
                $affected = DB::table('build_modversion')
                    ->where('build_id', '=', $build->id)
                    ->where('modversion_id', '=', $modversion_id)
                    ->update(['modversion_id' => $version_id]);
                if ($affected == 0) {
                    if ($modversion_id != $version_id) {
                        $status = 'failed';
                    } else {
                        $status = 'aborted';
                    }
                } else {
                    $status = 'success';
                    Cache::forget('modpack:'.$build->modpack->slug.':build:'.$build->version);
                }

                return response()->json([
                    'status' => $status,
                    'reason' => 'Rows Affected: '.$affected,
                ]);
            case 'delete': // Remove mod version from build
                $build = Build::with('modpack')->findOrFail(Request::input('build_id'));
                $this->authorize('update', [Build::class, $build->modpack]);
                $affected = DB::table('build_modversion')
                    ->where('build_id', '=', $build->id)
                    ->where('modversion_id', '=', Request::input('modversion_id'))
                    ->delete();
                $status = 'success';
                if ($affected == 0) {
                    $status = 'failed';
                } else {
                    Cache::forget('modpack:'.$build->modpack->slug.':build:'.$build->version);
                }

                return response()->json([
                    'status' => $status,
                    'reason' => 'Rows Affected: '.$affected,
                ]);
            case 'add': // Add mod version to build
                $build = Build::findOrFail(Request::input('build'));
                $this->authorize('update', [Build::class, $build->modpack]);
                $mod = Mod::where('name', '=', Request::input('mod-name'))->firstOrFail();
                $ver = Modversion::where('mod_id', '=', $mod->id)
                    ->where('version', '=', Request::input('mod-version'))
                    ->first();

                if (! $ver) {
                    return response()->json([
                        'status' => 'failed',
                        'reason' => 'No such mod version exists',
                    ]);
                }

                $existing = DB::table('build_modversion')
                    ->join('modversions', 'modversions.id', '=', 'build_modversion.modversion_id')
                    ->where('build_modversion.build_id', '=', $build->id)
                    ->where('modversions.mod_id', '=', $mod->id)
                    ->exists();
                if ($existing) {
                    return response()->json([
                        'status' => 'failed',
                        'reason' => 'This mod already exists in the build',
                    ]);
                } else {
                    $build->modversions()->attach($ver->id);

                    Cache::forget('modpack:'.$build->modpack->slug);
                    Cache::forget('modpack:'.$build->modpack->slug.':build:'.$build->version);

                    $allVersions = $mod->versions->map(fn (Modversion $v) => [
                        'id' => $v->id,
                        'version' => $v->version,
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'mod_id' => $mod->id,
                        'mod_name' => $mod->name,
                        'pretty_name' => $mod->pretty_name ?: $mod->name,
                        'version' => $ver->version,
                        'modversion_id' => $ver->id,
                        'versions' => $allVersions,
                    ]);
                }
            case 'recommended': // Set recommended build
                $modpack = Modpack::findOrFail(Request::input('modpack'));
                $this->authorize('update', $modpack);
                $new_version = Request::input('recommended');
                $modpack->recommended = $new_version;
                $modpack->save();

                Cache::forget('modpack:'.$modpack->slug);

                return response()->json([
                    'success' => 'Updated '.$modpack->name."'s recommended build to ".$new_version,
                    'version' => $new_version,
                ]);
            case 'latest': // Set latest build
                $modpack = Modpack::findOrFail(Request::input('modpack'));
                $this->authorize('update', $modpack);
                $new_version = Request::input('latest');
                $modpack->latest = $new_version;
                $modpack->save();

                Cache::forget('modpack:'.$modpack->slug);

                return response()->json([
                    'success' => 'Updated '.$modpack->name."'s latest build to ".$new_version,
                    'version' => $new_version,
                ]);
            case 'published': // Set build published status
                $build = Build::with('modpack')->findOrFail(Request::input('build'));
                $this->authorize('update', [Build::class, $build->modpack]);

                $build->is_published = \request()->boolean('published');
                $build->save();

                Cache::forget('modpack:'.$build->modpack->slug);
                Cache::forget('modpack:'.$build->modpack->slug.':build:'.$build->version);

                $state = $build->is_published ? 'published' : 'unpublished';

                return response()->json([
                    'success' => 'Build '.$build->version.' is now '.$state,
                ]);
            case 'private':
                $build = Build::with('modpack')->findOrFail(Request::input('build'));
                $this->authorize('update', [Build::class, $build->modpack]);

                $build->private = \request()->boolean('private');
                $build->save();

                Cache::forget('modpack:'.$build->modpack->slug);
                Cache::forget('modpack:'.$build->modpack->slug.':build:'.$build->version);

                $state = $build->private ? 'private' : 'public';

                return response()->json([
                    'success' => 'Build '.$build->version.' is now '.$state,
                ]);
            case 'batch-version': // Batch change mod versions in a build
                $build = Build::with('modpack')->findOrFail(Request::input('build_id'));
                $this->authorize('update', [Build::class, $build->modpack]);

                $changes = Request::input('changes', []);

                if (! is_array($changes)) {
                    return response()->json(['status' => 'error', 'reason' => 'Invalid changes format'], 422);
                }

                $validated = [];
                foreach ($changes as $change) {
                    if (! is_array($change)
                        || ! isset($change['old_modversion_id'], $change['new_modversion_id'])
                        || ! is_numeric($change['old_modversion_id'])
                        || ! is_numeric($change['new_modversion_id'])) {
                        return response()->json(['status' => 'error', 'reason' => 'Invalid change entry'], 422);
                    }
                    $validated[] = [
                        'old' => (int) $change['old_modversion_id'],
                        'new' => (int) $change['new_modversion_id'],
                    ];
                }

                $updated = DB::transaction(function () use ($build, $validated): int {
                    $count = 0;
                    foreach ($validated as $change) {
                        $affected = DB::table('build_modversion')
                            ->where('build_id', '=', $build->id)
                            ->where('modversion_id', '=', $change['old'])
                            ->update(['modversion_id' => $change['new']]);
                        $count += $affected;
                    }

                    return $count;
                });

                Cache::forget('modpack:'.$build->modpack->slug.':build:'.$build->version);

                return response()->json([
                    'status' => 'success',
                    'updated' => $updated,
                ]);
            default:
                abort(400);
        }
    }
}
