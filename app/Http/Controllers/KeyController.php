<?php

namespace App\Http\Controllers;

use App\Models\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class KeyController extends Controller
{
    public function __construct()
    {
        $this->middleware('solder_keys');
    }

    public function getIndex()
    {
        return redirect('key/list');
    }

    public function getList()
    {
        $keys = Key::all();

        return view('key.list')->with('keys', $keys);
    }

    public function getCreate()
    {
        return view('key.create');
    }

    public function postCreate()
    {
        $rules = [
            'name' => 'required|unique:keys',
            'api_key' => 'required|unique:keys',
        ];

        $validation = Validator::make(Request::all(), $rules);
        if ($validation->fails()) {
            return redirect('key/create')->withErrors($validation->messages());
        }

        $key = new Key();
        $key->name = Request::input('name');
        $key->api_key = Request::input('api_key');
        $key->save();
        Cache::forget('keys');

        return redirect('key/list')->with('success', 'API key added!');
    }

    public function getDelete($key_id)
    {
        $key = Key::find($key_id);

        if (empty($key)) {
            return redirect('key/list')->withErrors(new MessageBag(['Platform Key not found']));
        }

        return view('key.delete')->with('key', $key);
    }

    public function postDelete($key_id)
    {
        $key = Key::find($key_id);

        if (empty($key)) {
            return redirect('key/list')->withErrors(new MessageBag(['Platform Key not found']));
        }

        $key->delete();
        Cache::forget('keys');

        return redirect('key/list')->with('success', 'API Key deleted!');
    }
}
