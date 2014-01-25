<?php

class Key_Controller extends Base_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'auth');
		$this->filter('before', 'perm', array('solder_full'))->only('delete','do_delete');
	}

	public function action_list()
	{
		$keys = Key::all();
		return View::make('key.list')->with('keys', $keys);
	}

	public function action_create()
	{
		return View::make('key.create');
	}

	public function action_do_create()
	{
		$rules = array(
    		'name' => 'required|unique:keys',
    		'api_key' => 'required|unique:keys'
    		);

    	$validation = Validator::make(Input::all(), $rules);
    	if ($validation->fails())
    		return Redirect::back()->with_errors($validation->errors);

    	$key = new Key();
    	$key->name = Input::get('name');
    	$key->api_key = Input::get('api_key');
    	$key->save();

    	return Redirect::to('key/list')->with('success','API key added!');
	}

	public function action_delete($key_id)
	{
		$key = Key::find($key_id);

		if (empty($key))
			return Redirect::back();

		return View::make('key.delete')->with('key', $key);
	}

	public function action_do_delete($key_id)
	{
		$key = Key::find($key_id);

		if (empty($key))
			return Redirect::back();

		$key->delete();

		return Redirect::to('key/list')->with('success', 'API Key deleted!');
	}
}