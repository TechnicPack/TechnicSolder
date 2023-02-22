<?php

namespace App\Http\Controllers;

use App\Libraries\UrlUtils;
use App\Models\Mod;
use App\Models\Modversion;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ModController extends Controller
{
    public function __construct()
    {
        $this->middleware('solder_mods')->except('getModVersions');
    }

    public function getIndex(): RedirectResponse
    {
        return redirect('mod/list');
    }

    public function getList(): View
    {
        $mods = Mod::with(
            [
                'versions' => function ($query) {
                    $query->orderBy('modversions.updated_at', 'desc');
                },
            ]
        )
            ->get();

        return view('mod.list')->with(['mods' => $mods]);
    }

    public function getView($mod_id = null)
    {
        $mod = Mod::with('versions')
            ->with('versions.builds')
            ->with('versions.builds.modpack')
            ->find($mod_id);

        if (empty($mod)) {
            return redirect('mod/list')->withErrors(new MessageBag(['Mod not found']));
        }

        return view('mod.view')->with(['mod' => $mod]);
    }

    public function getCreate(): View
    {
        return view('mod.create');
    }

    public function postCreate(): RedirectResponse
    {
        $rules = [
            'name' => 'required|unique:mods',
            'pretty_name' => 'required',
            'link' => 'nullable|url',
        ];
        $messages = [
            'name.required' => 'You must fill in a mod slug name.',
            'name.unique' => 'The slug you entered is already taken',
            'pretty_name.required' => 'You must enter in a mod name',
            'link.url' => 'You must enter a properly formatted Website',
        ];

        $validation = Validator::make(Request::all(), $rules, $messages);
        if ($validation->fails()) {
            return redirect('mod/create')->withErrors($validation->messages());
        }

        $mod = new Mod();
        $mod->name = Str::slug(Request::input('name'));
        $mod->pretty_name = Request::input('pretty_name');
        $mod->author = Request::input('author');
        $mod->description = Request::input('description');
        $mod->link = Request::input('link');
        $mod->save();

        return redirect('mod/view/'.$mod->id);
    }

    public function getDelete($mod_id = null)
    {
        $mod = Mod::find($mod_id);
        if (empty($mod)) {
            return redirect('mod/list')->withErrors(new MessageBag(['Mod not found']));
        }

        return view('mod.delete')->with(['mod' => $mod]);
    }

    public function postModify($mod_id = null): RedirectResponse
    {
        $mod = Mod::find($mod_id);
        if (empty($mod)) {
            return redirect('mod/list')->withErrors(new MessageBag(['Error modifying mod - Mod not found']));
        }

        $rules = [
            'pretty_name' => 'required',
            'name' => 'required|unique:mods,name,'.$mod->id,
            'link' => 'nullable|url',
        ];

        $messages = [
            'name.required' => 'You must fill in a mod slug name.',
            'name.unique' => 'The slug you entered is already in use by another mod',
            'pretty_name.required' => 'You must enter in a mod name',
            'link.url' => 'You must enter a properly formatted Website',
        ];

        $validation = Validator::make(Request::all(), $rules, $messages);
        if ($validation->fails()) {
            return redirect('mod/view/'.$mod->id)->withErrors($validation->messages());
        }

        $mod->pretty_name = Request::input('pretty_name');
        $mod->name = Request::input('name');
        $mod->author = Request::input('author');
        $mod->description = Request::input('description');
        $mod->link = Request::input('link');
        $mod->save();
        Cache::forget('mod:'.$mod->name);

        return redirect('mod/view/'.$mod->id)->with('success', 'Mod successfully edited.');
    }

    public function postDelete($mod_id = null): RedirectResponse
    {
        $mod = Mod::find($mod_id);
        if (empty($mod)) {
            return redirect('mod/list')->withErrors(new MessageBag(['Error deleting mod - Mod not found']));
        }

        foreach ($mod->versions as $ver) {
            $ver->builds()->sync([]);
            $ver->delete();
        }
        $mod->delete();
        Cache::forget('mod:'.$mod->name);

        return redirect('mod/list')->with('success', 'Mod deleted!');
    }

    public function anyRehash(): JsonResponse
    {
        if (! Request::ajax()) {
            abort(404);
        }

        $md5 = Request::input('md5');
        $ver_id = Request::input('version-id');
        if (empty($ver_id)) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Missing Post Data',
            ]);
        }

        $ver = Modversion::find($ver_id);
        if (empty($ver)) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Could not pull mod version from database',
            ]);
        }

        if (empty($md5)) {
            $md5Request = $this->mod_md5($ver->mod, $ver->version);
            if ($md5Request['success']) {
                $md5 = $md5Request['md5'];
            }
        } else {
            $md5Request = $this->mod_md5($ver->mod, $ver->version);
            $providedfile_md5 = ! $md5Request['success'] ? 'Null' : $md5Request['md5'];
        }

        if ($md5Request['success'] && ! empty($md5)) {
            if ($md5 == $md5Request['md5']) {
                $ver->filesize = $md5Request['filesize'];
                $ver->md5 = $md5;
                $ver->save();

                return response()->json([
                    'status' => 'success',
                    'version_id' => $ver->id,
                    'md5' => $ver->md5,
                    'filesize' => $ver->humanFilesize(),
                ]);
            } else {
                $ver->filesize = $md5Request['filesize'];
                $ver->md5 = $md5;
                $ver->save();

                return response()->json([
                    'status' => 'warning',
                    'version_id' => $ver->id,
                    'md5' => $ver->md5,
                    'filesize' => $ver->humanFilesize(),
                    'reason' => 'MD5 provided does not match file MD5: '.$providedfile_md5,
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'reason' => 'Remote MD5 failed. '.$md5Request['message'],
            ]);
        }
    }

    public function anyAddVersion(): JsonResponse
    {
        if (! Request::ajax()) {
            abort(404);
        }

        $mod_id = Request::input('mod-id');
        $md5 = Request::input('add-md5');
        $version = Request::input('add-version');
        if (empty($mod_id) || empty($version)) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Missing Post Data',
            ]);
        }

        $mod = Mod::find($mod_id);
        if (empty($mod)) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Could not pull mod from database',
            ]);
        }

        if (Modversion::where([
            'mod_id' => $mod_id,
            'version' => $version,
        ])->count() > 0) {
            return response()->json([
                'status' => 'error',
                'reason' => 'That mod version already exists',
            ]);
        }

        if (empty($md5)) {
            $file_md5 = $this->mod_md5($mod, $version);
            if ($file_md5['success']) {
                $md5 = $file_md5['md5'];
            }
        } else {
            $file_md5 = $this->mod_md5($mod, $version);
            $pfile_md5 = ! $file_md5['success'] ? 'Null' : $file_md5['md5'];
        }

        $ver = new Modversion();
        $ver->mod_id = $mod->id;
        $ver->version = $version;

        if ($file_md5['success'] && ! empty($md5)) {
            if ($md5 === $file_md5['md5']) {
                $ver->filesize = $file_md5['filesize'];
                $ver->md5 = $md5;
                $ver->save();

                return response()->json([
                    'status' => 'success',
                    'version' => $ver->version,
                    'md5' => $ver->md5,
                    'filesize' => $ver->humanFilesize(),
                ]);
            } else {
                $ver->filesize = $file_md5['filesize'];
                $ver->md5 = $md5;
                $ver->save();

                return response()->json([
                    'status' => 'warning',
                    'version' => $ver->version,
                    'md5' => $ver->md5,
                    'filesize' => $ver->humanFilesize(),
                    'reason' => 'MD5 provided does not match file MD5: '.$pfile_md5,
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'reason' => 'Remote MD5 failed. '.$file_md5['message'],
            ]);
        }
    }

    public function anyDeleteVersion($ver_id = null): JsonResponse
    {
        if (! Request::ajax()) {
            abort(404);
        }

        if (empty($ver_id)) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Missing Post Data',
            ]);
        }

        $ver = Modversion::find($ver_id);
        if (empty($ver)) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Could not pull mod version from database',
            ]);
        }

        $old_id = $ver->id;
        $old_version = $ver->version;
        $ver->delete();

        return response()->json([
            'status' => 'success',
            'version' => $old_version,
            'version_id' => $old_id,
        ]);
    }

    public function getModVersions($modSlug)
    {
        if (! Request::ajax()) {
            abort(404);
        }

        if (empty($modSlug)) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Missing data',
            ]);
        }

        $mod = Cache::remember('mod:'.$modSlug, now()->addMinutes(5), function () use ($modSlug) {
            return Mod::with('versions')->where('name', $modSlug)->first();
        });

        if (! $mod) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Unknown mod',
            ]);
        }

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

    private function mod_md5($mod, $version)
    {
        $location = config('solder.repo_location');
        $URI = $location.'mods/'.$mod->name.'/'.$mod->name.'-'.$version.'.zip';

        if (filter_var($URI, FILTER_VALIDATE_URL)) {
            return $this->remote_mod_md5($mod, $version, $location);
        } elseif (file_exists($URI)) {
            Log::info('Found \''.$URI.'\'');
            try {
                $filesize = filesize($URI);
                $md5 = md5_file($URI);

                return ['success' => true, 'md5' => $md5, 'filesize' => $filesize];
            } catch (Exception $e) {
                Log::error('Error attempting to md5 the file: '.$URI);

                return ['success' => false, 'message' => $e->getMessage()];
            }
        } else {
            $error = $URI.' does not exist';
            Log::error($error);

            return ['success' => false, 'message' => $error];
        }
    }

    private function remote_mod_md5($mod, $version, $location, $attempts = 0)
    {
        $URL = $location.'mods/'.$mod->name.'/'.$mod->name.'-'.$version.'.zip';

        $hash = UrlUtils::get_remote_md5($URL);

        if (! ($hash['success']) && $attempts <= 3) {
            Log::warning('Error attempting to remote MD5 file '.$mod->name.' version '.$version.' located at '.$URL.'.');

            return $this->remote_mod_md5($mod, $version, $location, $attempts + 1);
        }

        return $hash;
    }
}
