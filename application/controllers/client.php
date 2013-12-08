<?php

class Client_Controller extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'auth');
		$this->filter('before', 'perm', array('solder_users'))->only('delete','do_delete');
	}

	public function action_list()
	{
		$clients = Client::all();
		return View::make('client.list')->with('clients', $clients);
	}

	public function action_create()
	{
		return View::make('client.create');
	}

	public function action_do_create()
	{
		$rules = array(
    		'name' => 'required|unique:clients',
    		'uuid' => 'required|unique:clients'
    		);

    	$validation = Validator::make(Input::all(), $rules);
    	if ($validation->fails())
    		return Redirect::back()->with_errors($validation->errors);

    	$client = new Client();
    	$client->name = Input::get('name');
    	$client->uuid = Input::get('uuid');
    	$client->save();

    	/* Immediately clear the cache */
    	Cache::forget('clients');

    	return Redirect::to('client/list')->with('success','Client added!');
	}

	public function action_delete($client_id)
	{
		$client = Client::find($client_id);

		if (empty($client))
			return Redirect::back();

		return View::make('client.delete')->with('client', $client);
	}

	public function action_do_delete($client_id)
	{
		$client = Client::find($client_id);

		if (empty($client))
			return Redirect::back();

		$client->modpacks()->delete();
		$client->delete();

		Cache::forget('clients');

		return Redirect::to('client/list')->with('success', 'Client deleted!');
	}

}