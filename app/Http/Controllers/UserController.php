<?php

namespace App\Http\Controllers;

use App\Models\Modpack;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Laravel\Fortify\Features;

class UserController extends Controller
{
    public function getIndex(): RedirectResponse
    {
        return redirect('user/list');
    }

    public function getList(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('updated_by_user')->get();

        return view('user.list')->with('users', $users);
    }

    public function getEdit($user_id = null)
    {
        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(['User ID not provided']);
        }

        $user = User::find($user_id);

        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(['User not found']);
        }

        $this->authorize('update', $user);

        $allModpacks = Modpack::all();

        $userUpdatedBy = User::find($user->updated_by_user_id);

        $showTwoFactor = Features::canManageTwoFactorAuthentication() && Auth::id() === $user->id;
        $qrCodeSvg = null;
        $recoveryCodes = null;

        if ($showTwoFactor && $user->two_factor_secret && ! $user->two_factor_confirmed_at) {
            $qrCodeSvg = $user->twoFactorQrCodeSvg();
        }

        if ($showTwoFactor && $user->two_factor_confirmed_at &&
            in_array(session('status'), ['two-factor-authentication-confirmed', 'recovery-codes-generated'])) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        }

        $adminViewingOther2FA = Auth::id() !== $user->id
            && (Auth::user()->permission->solder_full || Auth::user()->permission->solder_users)
            && $user->two_factor_confirmed_at;

        return view('user.edit')
            ->with('user', $user)
            ->with('allModpacks', $allModpacks)
            ->with('userUpdatedBy', $userUpdatedBy)
            ->with('showTwoFactor', $showTwoFactor)
            ->with('qrCodeSvg', $qrCodeSvg)
            ->with('recoveryCodes', $recoveryCodes)
            ->with('adminViewingOther2FA', $adminViewingOther2FA);
    }

    public function postEdit($user_id = null): RedirectResponse
    {
        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(['User ID not provided']);
        }

        $user = User::find($user_id);

        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(['User not found']);
        }

        $this->authorize('update', $user);

        $rules = [
            'email' => 'required|email|unique:users,email,'.$user_id,
            'username' => 'required|min:3|max:30|unique:users,username,'.$user_id,
        ];

        if (Request::input('password1')) {
            $rules['password1'] = [Password::defaults(), 'same:password2'];
        }

        $validation = Validator::make(Request::all(), $rules);

        if ($validation->fails()) {
            return redirect('user/edit/'.$user_id)->withErrors($validation->messages());
        }

        $user->email = Request::input('email');
        $user->username = Request::input('username');
        if (Request::input('password1')) {
            $user->password = Request::input('password1');
        }

        /* Update User Permissions */
        if (Auth::user()->permission->solder_full || Auth::user()->permission->solder_users) {
            $perm = $user->permission;

            /* If user is original admin, always give full access. */
            if ($user->id == 1) {
                $perm->solder_full = true;
            } elseif (Auth::user()->permission->solder_full) {
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
                $modpack = array_filter(array_map('intval', (array) $modpack));
                $perm->modpacks = Modpack::whereIn('id', $modpack)->pluck('id')->all() ?: null;
            } else {
                $perm->modpacks = null;
            }

            $perm->save();
        }

        // Security logging
        $user->updated_by_user_id = Auth::user()->id;
        $user->updated_by_ip = Request::ip();

        $user->save();

        $redirect = Auth::id() === $user->id ? 'user/edit/'.$user->id : 'user/list';

        return redirect($redirect)->with('success', 'User edited successfully!');
    }

    public function getCreate()
    {
        $this->authorize('create', User::class);

        $allModpacks = Modpack::all();

        return view('user.create')
            ->with('allModpacks', $allModpacks);
    }

    public function postCreate(): RedirectResponse
    {
        $this->authorize('create', User::class);

        $rules = [
            'email' => 'required|email|unique:users',
            'username' => 'required|min:3|max:30|unique:users',
            'password' => ['required', Password::defaults()],
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
        $user->password = Request::input('password');
        $user->created_ip = $creatorIP;
        $user->created_by_user_id = $creator;
        $user->updated_by_ip = $creatorIP;
        $user->updated_by_user_id = $creator;
        $user->save();

        $perm = new UserPermission;
        $perm->user_id = $user->id;

        $perm->solder_full = Auth::user()->permission->solder_full && \request()->boolean('solder-full');
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
            $modpack = array_filter(array_map('intval', (array) $modpack));
            $perm->modpacks = Modpack::whereIn('id', $modpack)->pluck('id')->all() ?: null;
        } else {
            $perm->modpacks = null;
        }

        $perm->save();

        return redirect('user/edit/'.$user->id)->with('success', 'User created!');
    }

    public function postResetTwoFactor($user_id): RedirectResponse
    {
        $this->authorize('delete', User::class);

        $user = User::find($user_id);

        if (empty($user)) {
            return redirect('user/list')->withErrors(['User not found']);
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect('user/edit/'.$user_id)->with('success', 'Two-factor authentication has been reset.');
    }

    public function getDelete($user_id = null)
    {
        $this->authorize('delete', User::class);

        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(['User ID not provided']);
        }

        $user = User::find($user_id);
        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(['User not found']);
        }

        if ($user->permission->solder_full) {
            $numOfOtherSuperUsers = DB::table('user_permissions')
                ->where('solder_full', true)
                ->whereNotIn('user_id', [$user_id])
                ->count();

            if ($numOfOtherSuperUsers <= 0) {
                return redirect('user/list')
                    ->withErrors(['Cannot delete the only remaining super user.']);
            }
        }

        return view('user.delete')->with(['user' => $user]);
    }

    public function postDelete($user_id = null): RedirectResponse
    {
        $this->authorize('delete', User::class);

        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(['User ID not provided']);
        }

        $user = User::find($user_id);
        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(['User not found']);
        }

        if ($user->permission->solder_full) {
            $numOfOtherSuperUsers = DB::table('user_permissions')
                ->where('solder_full', true)
                ->whereNotIn('user_id', [$user_id])
                ->count();

            if ($numOfOtherSuperUsers <= 0) {
                return redirect('user/list')
                    ->withErrors(['Cannot delete the only remaining super user.']);
            }
        }

        $user->permission()->delete();
        $user->tokens()->delete();
        $user->delete();

        return redirect('user/list')->with('success', 'User deleted!');
    }

    public function postCreateToken(): RedirectResponse
    {
        $user = Auth::user();

        $validation = Validator::make(Request::all(), [
            'token_name' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return redirect('user/edit/'.$user->id)->withErrors($validation->messages());
        }

        $token = $user->createToken(Request::input('token_name'));

        return redirect('user/edit/'.$user->id)->with('newToken', $token->plainTextToken);
    }

    public function postDeleteToken(int $tokenId): RedirectResponse
    {
        $user = Auth::user();

        $token = $user->tokens()->where('id', $tokenId)->first();

        if (! $token) {
            return redirect('user/edit/'.$user->id)->withErrors(['Token not found']);
        }

        $token->delete();

        return redirect('user/edit/'.$user->id)->with('success', 'API token revoked.');
    }
}
