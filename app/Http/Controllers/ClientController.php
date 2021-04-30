<?php namespace App\Http\Controllers;

use App\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class ClientController extends Controller
{

    public function __construct()
    {
        $this->middleware('solder_clients');
    }

    public function getIndex()
    {
        return redirect('client/list');
    }

    public function getList()
    {
        $clients = Client::all();
        return view('client.list')->with('clients', $clients);
    }

    public function getCreate()
    {
        return view('client.create');
    }

    public function postCreate()
    {
        $rules = [
            'name' => 'required|unique:clients',
            'uuid' => 'required|unique:clients'
        ];

        $validation = Validator::make(Request::all(), $rules);
        if ($validation->fails()) {
            return redirect('client/create')->withErrors($validation->messages());
        }

        $client = new Client();
        $client->name = Request::input('name');
        $client->uuid = Request::input('uuid');
        $client->save();

        /* Immediately clear the cache */
        Cache::forget('clients');

        return redirect('client/list')->with('success', 'Client added!');
    }

    public function getDelete($client_id)
    {
        $client = Client::find($client_id);

        if (empty($client)) {
            return redirect('client/list')->withErrors(new MessageBag(['Client UUID not found']));
        }

        return view('client.delete')->with('client', $client);
    }

    public function postDelete($client_id)
    {
        $client = Client::find($client_id);

        if (empty($client)) {
            return redirect('client/list')->withErrors(new MessageBag(['Client UUID not found']));
        }

        $client->modpacks()->sync([]);
        $client->delete();

        Cache::forget('clients');

        return redirect('client/list')->with('success', 'Client deleted!');
    }

}