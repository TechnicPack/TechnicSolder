<?php

class User_Controller extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'auth');
		$this->filter('before', 'perm', array('solder_users'))->except('list');
	}

	public function action_list()
	{
		$users = User::all();
		return View::make('user.list')->with('users', $users);
	}

	public function action_edit($user_id = null)
	{
		if (empty($user_id))
			return Redirect::to('user/list');

		$user = User::find($user_id);
		if (empty($user))
			return Redirect::to('user/list');

		if (Input::get('edit-user'))
		{
			$rules = array(
					"email" => "email|required",
					"username" => "required|max:20"
					);

			if (Input::get('password1'))
				$rules['password1'] = "same:password2";

			$validation = Validator::make(Input::all(), $rules);

			if ($validation->fails())
				return Redirect::back()->with_errors($validation);

			try {
				$user->email = Input::get('email');
				$user->username = Input::get('username');
				if (Input::get('password1'))
				{
					$user->password = Hash::make(Input::get('password1'));
				}

				/* Update User Permissions */
				$perm = $user->permission;

				/* If user is original admin, always give full access. */
				if ($user->id == 1)
				{
					$perm->solder_full = true;
				} else {
					$perm->solder_full = Input::get('solder-full') ? true : false;
				}
				$perm->solder_users = Input::get('manage-users') ? true : false;
				$perm->solder_modpacks = Input::get('manage-packs') ? true : false;
				$perm->solder_mods = Input::get('manage-mods') ? true : false;
				$perm->solder_create = Input::get('solder-create') ? true: false;

				$perm->mods_create = Input::get('mod-create') ? true : false;
				$perm->mods_manage = Input::get('mod-manage') ? true : false;
				$perm->mods_delete = Input::get('mod-delete') ? true : false;

				$modpack = Input::get('modpack');

				if (!empty($modpack))
					$perm->modpacks = $modpack;
				else
					$perm->modpacks = null;

				$perm->save();

				$user->save();

				return Redirect::back()->with('success','User edited successfully!');
			} catch (Exception $e) {
				Log::exception($e);
			}
		}

		return View::make('user.edit')->with('user', $user);
	}

    public function action_create()
    {
        return View::make('user.create');
    }
}