<?php

use Illuminate\Support\MessageBag;
class KeyController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->beforeFilter('solder_keys');
	}

	public function getIndex()
	{
		return Redirect::to('key/list');
	}

	public function getList()
	{
		$keys = Key::all();
		return View::make('key.list')->with('keys', $keys);
	}

	public function getCreate()
	{
		return View::make('key.create');
	}

	public function postCreate()
	{
		$rules = array(
			'name' => 'required|unique:keys',
			'api_key' => 'required|unique:keys'
			);

		$validation = Validator::make(Input::all(), $rules);
		if ($validation->fails())
			return Redirect::to('key/create')->withErrors($validation->messages());

		$key = new Key();
		$key->name = Input::get('name');
		$key->api_key = Input::get('api_key');
		$key->save();
		Cache::forget('keys');

		return Redirect::to('key/list')->with('success','API key added!');
	}

	public function getDelete($key_id)
	{
		$key = Key::find($key_id);

		if (empty($key))
			return Redirect::to('key/list')->withErrors(new MessageBag(array('Platform Key not found')));

		return View::make('key.delete')->with('key', $key);
	}

	public function postDelete($key_id)
	{
		$key = Key::find($key_id);

		if (empty($key))
			return Redirect::to('key/list')->withErrors(new MessageBag(array('Platform Key not found')));

		$key->delete();
		Cache::forget('keys');

		return Redirect::to('key/list')->with('success', 'API Key deleted!');
	}
}