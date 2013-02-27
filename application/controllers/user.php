<?php

class User_Controller extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'auth');
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
				$user->save();

				return Redirect::back()->with('success','User edited successfully!');
			} catch (Exception $e) {
				Log::exception($e);
			}
		}

		return View::make('user.edit')->with('user', $user);
	}
}