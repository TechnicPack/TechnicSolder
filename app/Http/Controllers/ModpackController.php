<?php namespace App\Http\Controllers;

use App\Build;
use App\Client;
use App\Libraries\MinecraftUtils;
use App\Mod;
use App\Modpack;
use App\Modversion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ModpackController extends Controller
{

    public function __construct()
    {
        $this->middleware('solder_modpacks');
        $this->middleware('modpack',
            ['only' => ['getView', 'getDelete', 'postDelete', 'getEdit', 'postEdit', 'getAddBuild', 'postAddBuild']]);
        $this->middleware('build', ['only' => ['anyBuild']]);
    }

    public function getIndex()
    {
        return redirect('modpack/list');
    }

    public function getList()
    {
        $modpacks = Modpack::all();
        return view('modpack.list')->with('modpacks', $modpacks);
    }

    public function getView($modpack_id = null)
    {
        $modpack = Modpack::with([
            'builds' => function ($query) {
                $query->withCount('modversions');
            }
        ])
            ->find($modpack_id);

        if (empty($modpack)) {
            return redirect('modpack/list')->withErrors(new MessageBag(['Modpack not found']));
        }

        return view('modpack.view')->with('modpack', $modpack);
    }

    public function anyBuild($build_id = null)
    {
        $build = Build::with('modpack')
            ->with('modversions')
            ->with('modversions.mod')
            ->with('modversions.mod.versions')
            ->find($build_id);
        if (empty($build)) {
            return redirect('modpack/list')->withErrors(new MessageBag(['Modpack not found']));
        }

        if (Request::input('action') == "delete") {
            if (Request::input('confirm-delete')) {
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
                    $modpack->recommended = $recbuild->version;
                }

                if ($switchlat) {
                    $latbuild = Build::where('modpack_id', '=', $modpack->id)
                        ->orderBy('id', 'desc')->first();
                    $modpack->latest = $latbuild->version;
                }
                $modpack->save();
                Cache::forget('modpack:' . $modpack->slug);
                Cache::forget('modpack:' . $modpack->slug . ':build:' . $buildVersion);
                return redirect('modpack/view/' . $build->modpack->id)->with('deleted', 'Build deleted.');
            }

            return view('modpack.build.delete')->with('build', $build);
        } else {
            if (Request::input('action') == "edit") {
                if (Request::input('confirm-edit')) {
                    $rules = [
                        "version" => "required",
                        "minecraft" => "required",
                        "memory" => "numeric"
                    ];

                    $messages = [
                        'version.required' => "You must enter in the build number.",
                        'memory.numeric' => "You may enter in numbers only for the memory requirement"
                    ];

                    $validation = Validator::make(Request::all(), $rules, $messages);
                    if ($validation->fails()) {
                        return redirect('modpack/build/' . $build->id . '?action=edit')->withErrors($validation->messages());
                    }

                    $build->version = Request::input('version');

                    $minecraft = Request::input('minecraft');

                    $build->minecraft = $minecraft;
                    $build->min_java = Request::input('java-version');
                    $build->min_memory = Request::input('memory-enabled') ? Request::input('memory') : 0;
                    $build->save();
                    Cache::forget('modpack:' . $build->modpack->slug);
                    Cache::forget('modpack:' . $build->modpack->slug . ':build:' . $build->version);
                    return redirect('modpack/build/' . $build->id);
                }
                $minecraft = MinecraftUtils::getMinecraft();
                return view('modpack.build.edit')->with('build', $build)->with('minecraft', $minecraft);
            } else {
                $mods = Mod::all();

                return view('modpack.build.view')
                    ->with('build', $build)
                    ->with('mods', $mods);
            }
        }
    }

    public function getAddBuild($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list')->withErrors(new MessageBag(['Modpack not found']));
        }

        $minecraft = MinecraftUtils::getMinecraft();

        return view('modpack.build.create')
            ->with([
                'modpack' => $modpack,
                'minecraft' => $minecraft
            ]);
    }

    public function postAddBuild($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list')->withErrors(new MessageBag(['Modpack not found']));
        }

        $rules = [
            "version" => [
                "required",
                Rule::unique('builds')->where(function ($query) use ($modpack) {
                    return $query->where('modpack_id', $modpack->id);
                })
            ],
            "minecraft" => "required",
            "memory" => "numeric"
        ];

        $messages = [
            'version.required' => "You must enter in the build number.",
            'memory.numeric' => "You may enter in numbers only for the memory requirement"
        ];

        $validation = Validator::make(Request::all(), $rules, $messages);
        if ($validation->fails()) {
            return redirect('modpack/add-build/' . $modpack_id)->withErrors($validation->messages());
        }

        $clone = Request::input('clone');
        $build = new Build();
        $build->modpack_id = $modpack->id;
        $build->version = Request::input('version');

        $minecraft = Request::input('minecraft');

        $build->minecraft = $minecraft;
        $build->min_java = Request::input('java-version');
        $build->min_memory = Request::input('memory-enabled') ? Request::input('memory') : 0;
        $build->save();
        Cache::forget('modpack:' . $modpack->slug);
        if (!empty($clone)) {
            $clone_build = Build::find($clone);
            $version_ids = [];
            foreach ($clone_build->modversions as $cver) {
                if (!empty($cver)) {
                    array_push($version_ids, $cver->id);
                }
            }
            $build->modversions()->sync($version_ids);
        }

        return redirect('modpack/build/' . $build->id);
    }

    public function getCreate()
    {
        return view('modpack.create');
    }

    public function postCreate()
    {

        $rules = [
            'name' => 'required|unique:modpacks',
            'slug' => 'required|unique:modpacks'
        ];

        $messages = [
            'name_required' => 'You must enter a modpack name.',
            'slug_required' => 'You must enter a modpack slug'
        ];

        $validation = Validator::make(Request::all(), $rules, $messages);

        if ($validation->fails()) {
            return redirect('modpack/create')->withErrors($validation->messages());
        }

        $modpack = new Modpack();
        $modpack->name = Request::input('name');
        $modpack->slug = Str::slug(Request::input('slug'));
        $modpack->hidden = Request::input('hidden') ? false : true;
        $modpack->icon_md5 = null;
        $modpack->icon_url = URL::asset('/resources/default/icon.png');
        $modpack->logo_md5 = null;
        $modpack->logo_url = URL::asset('/resources/default/logo.png');
        $modpack->background_md5 = null;
        $modpack->background_url = URL::asset('/resources/default/background.jpg');
        $modpack->save();

        /* Gives creator modpack perms */
        $user = Auth::user();
        $perm = $user->permission;
        $modpacks = $perm->modpacks;
        if (!empty($modpacks)) {
            Log::info($modpack->name . ': Attempting to add modpack perm to user - ' . $user->username . ' - Modpack perm not empty');
            $newmodpacks = array_merge($modpacks, [$modpack->id]);
            $perm->modpacks = $newmodpacks;
        } else {
            Log::info($modpack->name . ': Attempting to add modpack perm to user - ' . $user->username . ' - Modpack perm empty');
            $perm->modpacks = [$modpack->id];
        }
        $perm->save();

        return redirect('modpack/view/' . $modpack->id);
    }

    public function getEdit($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('dashboard')->withErrors(new MessageBag(['Modpack not found']));
        }

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

    public function postEdit($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list/')->withErrors(new MessageBag(['Modpack not found']));
        }

        $rules = [
            'name' => 'required|unique:modpacks,name,' . $modpack->id,
            'slug' => 'required|unique:modpacks,slug,' . $modpack->id
        ];

        $messages = [
            'name_required' => 'You must enter a modpack name.',
            'slug_required' => 'You must enter a modpack slug'
        ];

        $validation = Validator::make(Request::all(), $rules, $messages);
        if ($validation->fails()) {
            return redirect('modpack/edit/' . $modpack_id)->withErrors($validation->messages());
        }

        $modpack->name = Request::input('name');
        $modpack->slug = Request::input('slug');
        $modpack->hidden = Request::boolean('hidden');
        $modpack->private = Request::boolean('private');
        $modpack->save();

        Cache::forget('modpack:' . $modpack->slug);
        Cache::forget('modpacks');

        /* Client Syncing */
        $clients = Request::input('clients');
        if ($clients) {
            $modpack->clients()->sync($clients);
        } else {
            $modpack->clients()->sync([]);
        }

        return redirect('modpack/view/' . $modpack->id)->with('success', 'Modpack edited');
    }

    public function getDelete($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list/')->withErrors(new MessageBag(['Modpack not found']));
        }

        return view('modpack.delete')->with(['modpack' => $modpack]);
    }

    public function postDelete($modpack_id)
    {
        $modpack = Modpack::find($modpack_id);
        if (empty($modpack)) {
            return redirect('modpack/list/')->withErrors(new MessageBag(['Modpack not found']));
        }

        foreach ($modpack->builds as $build) {
            $build->modversions()->sync([]);
            $build->delete();
        }

        $modpack->clients()->sync([]);
        $modpack->delete();
        Cache::forget('modpacks');

        return redirect('modpack/list/')->with('success', 'Modpack Deleted');
    }


    /**
     * AJAX Methods for Modpack Manager
     **/
    public function anyModify($action = null)
    {
        if (!Request::ajax()) {
            abort(404);
        }

        if (empty($action)) {
            abort(400);
        }

        switch ($action) {
            case "version": // Change mod version in a build
                $version_id = Request::input('version');
                $modversion_id = Request::input('modversion_id');
                $affected = DB::table('build_modversion')
                    ->where('build_id', '=', Request::input('build_id'))
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
                }
                return response()->json([
                    'status' => $status,
                    'reason' => 'Rows Affected: ' . $affected
                ]);
            case "delete": // Remove mod version from build
                $affected = DB::table('build_modversion')
                    ->where('build_id', '=', Request::input('build_id'))
                    ->where('modversion_id', '=', Request::input('modversion_id'))
                    ->delete();
                $status = 'success';
                if ($affected == 0) {
                    $status = 'failed';
                }
                return response()->json([
                    'status' => $status,
                    'reason' => 'Rows Affected: ' . $affected
                ]);
            case "add": // Add mod version to build
                $build = Build::find(Request::input('build'));
                $mod = Mod::where('name', '=', Request::input('mod-name'))->first();
                $ver = Modversion::where('mod_id', '=', $mod->id)
                    ->where('version', '=', Request::input('mod-version'))
                    ->first();

                if (!$ver) {
                    return response()->json([
                        'status' => 'failed',
                        'reason' => 'No such mod version exists'
                    ]);
                }

                $duplicate = DB::table('build_modversion')
                        ->where('build_id', '=', $build->id)
                        ->where('modversion_id', '=', $ver->id)
                        ->count() > 0;
                if ($duplicate) {
                    return response()->json([
                        'status' => 'failed',
                        'reason' => 'Duplicate Modversion found'
                    ]);
                } else {
                    $build->modversions()->attach($ver->id);

                    Cache::forget('modpack:' . $build->modpack->slug);
                    Cache::forget('modpack:' . $build->modpack->slug . ':build:' . $build->version);

                    return response()->json([
                        'status' => 'success',
                        'pretty_name' => $mod->pretty_name,
                        'version' => $ver->version
                    ]);
                }
            case "recommended": // Set recommended build
                $modpack = Modpack::find(Request::input('modpack'));
                $new_version = Request::input('recommended');
                $modpack->recommended = $new_version;
                $modpack->save();

                Cache::forget('modpack:' . $modpack->slug);

                return response()->json([
                    "success" => "Updated " . $modpack->name . "'s recommended  build to " . $new_version,
                    "version" => $new_version
                ]);
            case "latest": // Set latest build
                $modpack = Modpack::find(Request::input('modpack'));
                $new_version = Request::input('latest');
                $modpack->latest = $new_version;
                $modpack->save();

                Cache::forget('modpack:' . $modpack->slug);

                return response()->json([
                    "success" => "Updated " . $modpack->name . "'s latest  build to " . $new_version,
                    "version" => $new_version
                ]);
            case "published": // Set build published status
                $build = Build::with('modpack')->find(Request::input('build'));
                $published = Request::input('published');

                $build->is_published = ($published ? true : false);
                $build->save();

                Cache::forget('modpack:' . $build->modpack->slug);
                Cache::forget('modpack:' . $build->modpack->slug . ':build:' . $build->version);

                return response()->json([
                    "success" => "Updated build " . $build->version . "'s published status.",
                ]);
            case "private":
                $build = Build::with('modpack')->find(Request::input('build'));
                $private = Request::input('private');

                $build->private = ($private ? true : false);
                $build->save();

                Cache::forget('modpack:' . $build->modpack->slug);
                Cache::forget('modpack:' . $build->modpack->slug . ':build:' . $build->version);

                return response()->json([
                    "success" => "Updated build " . $build->version . "'s private status.",
                ]);
        }
    }
}
