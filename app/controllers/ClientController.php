<?php

use Illuminate\Support\MessageBag;
class ClientController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->beforeFilter('solder_clients');
	}

	public function getIndex()
	{
		return Redirect::to('client/list');
	}

	public function getList()
	{
		$clients = Client::all();
		return View::make('client.list')->with('clients', $clients);
	}

	public function getCreate()
	{
		return View::make('client.create');
	}

	public function postCreate()
	{
		$rules = array(
			'name' => 'required|unique:clients',
			'uuid' => 'required|unique:clients'
			);

		$validation = Validator::make(Input::all(), $rules);
		if ($validation->fails())
			return Redirect::to('client/create')->withErrors($validation->messages());

		$client = new Client();
		$client->name = Input::get('name');
		$client->uuid = Input::get('uuid');
		$client->save();

		/* Immediately clear the cache */
		Cache::forget('clients');

		return Redirect::to('client/list')->with('success','Client added!');
	}

	public function getDelete($client_id)
	{
		$client = Client::find($client_id);

		if (empty($client))
			return Redirect::to('client/list')->withErrors(new MessageBag(array('Client UUID not found')));

		return View::make('client.delete')->with('client', $client);
	}

	public function postDelete($client_id)
	{
		$client = Client::find($client_id);

		if (empty($client))
			return Redirect::to('client/list')->withErrors(new MessageBag(array('Client UUID not found')));

		$client->modpacks()->sync(array());
		$client->delete();

		Cache::forget('clients');

		return Redirect::to('client/list')->with('success', 'Client deleted!');
	}

}