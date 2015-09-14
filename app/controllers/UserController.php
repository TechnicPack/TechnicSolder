<?php

use Illuminate\Support\MessageBag;
class UserController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->beforeFilter('solder_users');
	}

	public function getIndex()
	{
		return Redirect::to('user/list');
	}

	public function getList()
	{
		$users = User::all();
		return View::make('user.list')->with('users', $users);
	}

	public function getEdit($user_id = null)
	{
		if (empty($user_id))
			return Redirect::to('user/list')
				->withErrors(new MessageBag(array('User ID not provided')));

		$user = User::find($user_id);

		if (empty($user))
			return Redirect::to('user/list')
				->withErrors(new MessageBag(array('User not found')));

		return View::make('user.edit')->with('user', $user);
	}

	public function postEdit($user_id = null)
	{
		if (empty($user_id))
			return Redirect::to('user/list')
				->withErrors(new MessageBag(array('User ID not provided')));

		if (!Auth::user()->permission->solder_full && !Auth::user()->permission->solder_users && $user_id != Auth::user()->id)
			return Redirect::to('dashboard')
				->with('permission','You do not have permission to access this area.');

		$user = User::find($user_id);

		if (empty($user))
			return Redirect::to('user/list')
				->withErrors(new MessageBag(array('User not found')));

		$rules = array(
				'email' => 'required|email|unique:users,email,' . $user_id,
				'username' => 'required|min:3|max:30|unique:users,username,' . $user_id
				);

		if (Input::get('password1'))
			$rules['password1'] = "min:3|same:password2";

		$validation = Validator::make(Input::all(), $rules);

		if ($validation->fails())
			return Redirect::to('user/edit/'. $user_id)->withErrors($validation->messages());

		$user->email = Input::get('email');
		$user->username = Input::get('username');
		if (Input::get('password1'))
		{
			$user->password = Hash::make(Input::get('password1'));
		}

		/* Update User Permissions */
		if (Auth::user()->permission->solder_full || Auth::user()->permission->solder_users)
		{
			$perm = $user->permission;

			/* If user is original admin, always give full access. */
			if ($user->id == 1)
			{
				$perm->solder_full = true;
			} else {
				$perm->solder_full = Input::get('solder-full') ? true : false;
			}
			$perm->solder_users = Input::get('manage-users') ? true : false;
			$perm->solder_keys = Input::get('manage-keys') ? true : false;
			$perm->solder_clients = Input::get('manage-clients') ? true : false;

			/* Mod Perms */
			$perm->mods_create = Input::get('mod-create') ? true : false;
			$perm->mods_manage = Input::get('mod-manage') ? true : false;
			$perm->mods_delete = Input::get('mod-delete') ? true : false;

			/* Modpack Perms */
			$perm->modpacks_create = Input::get('modpack-create') ? true : false;
			$perm->modpacks_manage = Input::get('modpack-manage') ? true : false;
			$perm->modpacks_delete = Input::get('modpack-delete') ? true : false;
			$modpack = Input::get('modpack');

			if (!empty($modpack))
				$perm->modpacks = $modpack;
			else
				$perm->modpacks = null;

			$perm->save();
		}

		//Security logging
		$user->updated_by_user_id = Auth::user()->id;
		$user->updated_by_ip = Request::ip();

		$user->save();

		return Redirect::to('user/list')->with('success','User edited successfully!');
	}

	public function getCreate()
	{
		if (!Auth::user()->permission->solder_full && !Auth::user()->permission->solder_users)
			return Redirect::to('dashboard')
				->with('permission','You do not have permission to access this area.');

		return View::make('user.create');
	}

	public function postCreate()
	{	
		$rules = array(
			'email' => 'required|email|unique:users',
			'username' => 'required|min:3|max:30|unique:users',
			'password' => 'required|min:3'
			);

		$validation = Validator::make(Input::all(), $rules);
		if ($validation->fails())
			return Redirect::to('user/create')->withErrors($validation->messages());

		$creator = Auth::user()->id;
		$creatorIP = Request::ip();

		$user = new User();
		$user->email = Input::get('email');
		$user->username = Input::get('username');
		$user->password = Hash::make(Input::get('password'));
		$user->created_ip = $creatorIP;
		$user->created_by_user_id = $creator;
		$user->updated_by_ip = $creatorIP;
		$user->updated_by_user_id = $creator;
		$user->save();

		$perm = new UserPermission();
		$perm->user_id = $user->id;

		$perm->solder_full = Input::get('solder-full') ? true : false;
		$perm->solder_users = Input::get('manage-users') ? true : false;
		$perm->solder_keys = Input::get('manage-keys') ? true : false;
		$perm->solder_clients = Input::get('manage-clients') ? true : false;

		/* Mod Perms */
		$perm->mods_create = Input::get('mod-create') ? true : false;
		$perm->mods_manage = Input::get('mod-manage') ? true : false;
		$perm->mods_delete = Input::get('mod-delete') ? true : false;

		/* Modpack Perms */
		$perm->modpacks_create = Input::get('modpack-create') ? true : false;
		$perm->modpacks_manage = Input::get('modpack-manage') ? true : false;
		$perm->modpacks_delete = Input::get('modpack-delete') ? true : false;
		$modpack = Input::get('modpack');

		if (!empty($modpack))
			$perm->modpacks = $modpack;
		else
			$perm->modpacks = null;

		$perm->save();

		return Redirect::to('user/edit/'.$user->id)->with('success','User created!');
	}

	public function getDelete($user_id = null)
	{
		if (!Auth::user()->permission->solder_full && !Auth::user()->permission->solder_users)
			return Redirect::to('dashboard')
				->with('permission','You do not have permission to access this area.');

		if (empty($user_id))
			return Redirect::to('user/list')
				->withErrors(new MessageBag(array('User ID not provided')));

		$user = User::find($user_id);
		if (empty($user))
			return Redirect::to('user/list')
				->withErrors(new MessageBag(array('User not found')));

		if($user->permission->solder_full){
			$numOfOtherSuperUsers = DB::table('user_permissions')
				->where('solder_full', TRUE)
				->whereNotIn('user_id', array($user_id))
				->count();

			if($numOfOtherSuperUsers <= 0)
				return Redirect::to('user/list')
					->withErrors(new MessageBag(array('Cannot delete the only remaining super user.')));
		}

		return View::make('user.delete')->with(array('user' => $user));
	}

	public function postDelete($user_id = null)
	{
		if (empty($user_id))
			return Redirect::to('user/list')
				->withErrors(new MessageBag(array('User ID not provided')));

		$user = User::find($user_id);
		if (empty($user))
			return Redirect::to('user/list')
				->withErrors(new MessageBag(array('User not found')));

		if($user->permission->solder_full){
			$numOfOtherSuperUsers = DB::table('user_permissions')
				->where('solder_full', TRUE)
				->whereNotIn('user_id', array($user_id))
				->count();

			if($numOfOtherSuperUsers <= 0)
				return Redirect::to('user/list')
					->withErrors(new MessageBag(array('Cannot delete the only remaining super user.')));
		}

		$user->permission()->delete();
		$user->delete();

		return Redirect::to('user/list')->with('success','User deleted!');
	}
}
