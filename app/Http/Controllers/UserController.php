<?php

namespace App\Http\Controllers;

use App\Models\Modpack;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('solder_users');
    }

    public function getIndex(): RedirectResponse
    {
        return redirect('user/list');
    }

    public function getList(): View
    {
        $users = User::with('updated_by_user')->get();

        return view('user.list')->with('users', $users);
    }

    public function getEdit($user_id = null)
    {
        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(new MessageBag(['User ID not provided']));
        }

        $user = User::find($user_id);

        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(new MessageBag(['User not found']));
        }

        $allModpacks = Modpack::all();

        $userUpdatedBy = User::find($user->updated_by_user_id);

        return view('user.edit')
            ->with('user', $user)
            ->with('allModpacks', $allModpacks)
            ->with('userUpdatedBy', $userUpdatedBy);
    }

    public function postEdit($user_id = null): RedirectResponse
    {
        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(new MessageBag(['User ID not provided']));
        }

        if (! Auth::user()->permission->solder_full && ! Auth::user()->permission->solder_users && $user_id != Auth::user()->id) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        $user = User::find($user_id);

        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(new MessageBag(['User not found']));
        }

        $rules = [
            'email' => 'required|email|unique:users,email,'.$user_id,
            'username' => 'required|min:3|max:30|unique:users,username,'.$user_id,
        ];

        if (Request::input('password1')) {
            $rules['password1'] = 'min:3|same:password2';
        }

        $validation = Validator::make(Request::all(), $rules);

        if ($validation->fails()) {
            return redirect('user/edit/'.$user_id)->withErrors($validation->messages());
        }

        $user->email = Request::input('email');
        $user->username = Request::input('username');
        if (Request::input('password1')) {
            $user->password = Hash::make(Request::input('password1'));
        }

        /* Update User Permissions */
        if (Auth::user()->permission->solder_full || Auth::user()->permission->solder_users) {
            $perm = $user->permission;

            /* If user is original admin, always give full access. */
            if ($user->id == 1) {
                $perm->solder_full = true;
            } else {
                $perm->solder_full = \request()->boolean('solder-full');
            }
            $perm->solder_users = \request()->boolean('manage-users');
            $perm->solder_keys = \request()->boolean('manage-keys');
            $perm->solder_clients = \request()->boolean('manage-clients');

            /* Mod Perms */
            $perm->mods_create = \request()->boolean('mod-create');
            $perm->mods_manage = \request()->boolean('mod-manage');
            $perm->mods_delete = \request()->boolean('mod-delete');

            /* Modpack Perms */
            $perm->modpacks_create = \request()->boolean('modpack-create');
            $perm->modpacks_manage = \request()->boolean('modpack-manage');
            $perm->modpacks_delete = \request()->boolean('modpack-delete');
            $modpack = Request::input('modpack');

            if (! empty($modpack)) {
                $perm->modpacks = $modpack;
            } else {
                $perm->modpacks = null;
            }

            $perm->save();
        }

        // Security logging
        $user->updated_by_user_id = Auth::user()->id;
        $user->updated_by_ip = Request::ip();

        $user->save();

        return redirect('user/list')->with('success', 'User edited successfully!');
    }

    public function getCreate()
    {
        if (! Auth::user()->permission->solder_full && ! Auth::user()->permission->solder_users) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        $allModpacks = Modpack::all();

        return view('user.create')
            ->with('allModpacks', $allModpacks);
    }

    public function postCreate(): RedirectResponse
    {
        $rules = [
            'email' => 'required|email|unique:users',
            'username' => 'required|min:3|max:30|unique:users',
            'password' => 'required|min:3',
        ];

        $validation = Validator::make(Request::all(), $rules);
        if ($validation->fails()) {
            return redirect('user/create')->withErrors($validation->messages());
        }

        $creator = Auth::user()->id;
        $creatorIP = Request::ip();

        $user = new User;
        $user->email = Request::input('email');
        $user->username = Request::input('username');
        $user->password = Hash::make(Request::input('password'));
        $user->created_ip = $creatorIP;
        $user->created_by_user_id = $creator;
        $user->updated_by_ip = $creatorIP;
        $user->updated_by_user_id = $creator;
        $user->save();

        $perm = new UserPermission;
        $perm->user_id = $user->id;

        $perm->solder_full = \request()->boolean('solder-full');
        $perm->solder_users = \request()->boolean('manage-users');
        $perm->solder_keys = \request()->boolean('manage-keys');
        $perm->solder_clients = \request()->boolean('manage-clients');

        /* Mod Perms */
        $perm->mods_create = \request()->boolean('mod-create');
        $perm->mods_manage = \request()->boolean('mod-manage');
        $perm->mods_delete = \request()->boolean('mod-delete');

        /* Modpack Perms */
        $perm->modpacks_create = \request()->boolean('modpack-create');
        $perm->modpacks_manage = \request()->boolean('modpack-manage');
        $perm->modpacks_delete = \request()->boolean('modpack-delete');
        $modpack = Request::input('modpack');

        if (! empty($modpack)) {
            $perm->modpacks = $modpack;
        } else {
            $perm->modpacks = null;
        }

        $perm->save();

        return redirect('user/edit/'.$user->id)->with('success', 'User created!');
    }

    public function getDelete($user_id = null)
    {
        if (! Auth::user()->permission->solder_full && ! Auth::user()->permission->solder_users) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(new MessageBag(['User ID not provided']));
        }

        $user = User::find($user_id);
        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(new MessageBag(['User not found']));
        }

        if ($user->permission->solder_full) {
            $numOfOtherSuperUsers = DB::table('user_permissions')
                ->where('solder_full', true)
                ->whereNotIn('user_id', [$user_id])
                ->count();

            if ($numOfOtherSuperUsers <= 0) {
                return redirect('user/list')
                    ->withErrors(new MessageBag(['Cannot delete the only remaining super user.']));
            }
        }

        return view('user.delete')->with(['user' => $user]);
    }

    public function postDelete($user_id = null): RedirectResponse
    {
        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(new MessageBag(['User ID not provided']));
        }

        $user = User::find($user_id);
        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(new MessageBag(['User not found']));
        }

        if ($user->permission->solder_full) {
            $numOfOtherSuperUsers = DB::table('user_permissions')
                ->where('solder_full', true)
                ->whereNotIn('user_id', [$user_id])
                ->count();

            if ($numOfOtherSuperUsers <= 0) {
                return redirect('user/list')
                    ->withErrors(new MessageBag(['Cannot delete the only remaining super user.']));
            }
        }

        $user->permission()->delete();
        $user->delete();

        return redirect('user/list')->with('success', 'User deleted!');
    }
}
