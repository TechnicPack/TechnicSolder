<?php

namespace App\Http\Controllers;

use App\Models\Modpack;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\Collection;
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

        // Gate non-managers out before the lookup so the response can't be used
        // to tell which user ids exist. Editing yourself needs no permission.
        if ((int) $user_id !== Auth::id()) {
            $this->authorize('viewAny', User::class);
        }

        $user = User::find($user_id);

        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(['User not found']);
        }

        $this->authorize('update', $user);

        $allModpacks = $this->grantableModpackList();

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

        // Gate non-managers out before the lookup so the response can't be used
        // to tell which user ids exist. Editing yourself needs no permission.
        if ((int) $user_id !== Auth::id()) {
            $this->authorize('viewAny', User::class);
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
            // Proof of identity for a session takeover, not proof of the target's
            // password: `current_password:web` checks the *actor*, so a manager
            // resetting someone else's password confirms their own.
            $rules['current_password'] = ['bail', 'required', 'string', 'current_password:web'];
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

            $this->applyGrantablePermissions($perm, Auth::user(), (int) $user->id === 1);

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

        $allModpacks = $this->grantableModpackList();

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

        // The user and its permission row are written together: a failure between
        // the two would otherwise leave a user with no permissions at all, which
        // no manager can then act on.
        $user = DB::transaction(function () use ($creator, $creatorIP) {
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

            $this->applyGrantablePermissions($perm, Auth::user(), false);

            $perm->save();

            return $user;
        });

        return redirect('user/edit/'.$user->id)->with('success', 'User created!');
    }

    public function postResetTwoFactor($user_id): RedirectResponse
    {
        // Admin-only recovery action: gate non-managers out before the lookup
        // (avoids leaking which user ids exist), then confirm this manager may
        // act on the target. Deliberately not the self-passing `update` ability.
        $this->authorize('viewAny', User::class);

        $user = User::find($user_id);

        if (empty($user)) {
            return redirect('user/list')->withErrors(['User not found']);
        }

        $this->authorize('delete', $user);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect('user/edit/'.$user_id)->with('success', 'Two-factor authentication has been reset.');
    }

    public function getDelete($user_id = null)
    {
        $this->authorize('viewAny', User::class);

        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(['User ID not provided']);
        }

        $user = User::find($user_id);
        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(['User not found']);
        }

        $this->authorize('delete', $user);

        if ((int) $user_id === Auth::id()) {
            return redirect('user/list')
                ->withErrors(['You cannot delete your own account.']);
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
        $this->authorize('viewAny', User::class);

        if (empty($user_id)) {
            return redirect('user/list')
                ->withErrors(['User ID not provided']);
        }

        $user = User::find($user_id);
        if (empty($user)) {
            return redirect('user/list')
                ->withErrors(['User not found']);
        }

        $this->authorize('delete', $user);

        if ((int) $user_id === Auth::id()) {
            return redirect('user/list')
                ->withErrors(['You cannot delete your own account.']);
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

    /**
     * Assign permissions to $perm from the request, clamped to what the acting
     * user is allowed to grant. A non-superadmin may only grant (or revoke)
     * permissions they hold themselves; permissions they lack are left at the
     * target's existing value. Only a solder_full user may grant anything.
     */
    private function applyGrantablePermissions(UserPermission $perm, User $actor, bool $isOriginalAdmin): void
    {
        foreach (UserPermission::GRANTABLE_FIELDS as $column => $field) {
            if ($actor->can('grant-permission', $column)) {
                $perm->{$column} = \request()->boolean($field);
            }
        }

        // The original admin (user 1) is always a superadmin.
        if ($isOriginalAdmin) {
            $perm->solder_full = true;
        }

        $perm->modpacks = $this->grantableModpacks($actor);
    }

    /**
     * The per-modpack scope to assign, clamped to what the actor may grant: the
     * requested ids intersected with the actor's own scope (the full requested
     * set for a solder_full actor), validated against existing modpacks.
     *
     * @return list<int>|null
     */
    private function grantableModpacks(User $actor): ?array
    {
        $requested = array_filter(array_map('intval', (array) Request::input('modpack')));

        if (! $actor->permission->solder_full) {
            $actorPacks = array_map('intval', $actor->permission->modpacks);
            $requested = array_intersect($requested, $actorPacks);
        }

        return Modpack::whereIn('id', $requested)->pluck('id')->all() ?: null;
    }

    /**
     * The modpacks the acting user may assign in the create/edit forms: all
     * modpacks for a solder_full user, otherwise only those in their own scope.
     *
     * @return Collection<int, Modpack>
     */
    private function grantableModpackList(): Collection
    {
        $actorPerm = Auth::user()->permission;

        return $actorPerm->solder_full
            ? Modpack::all()
            : Modpack::whereIn('id', $actorPerm->modpacks)->get();
    }
}
