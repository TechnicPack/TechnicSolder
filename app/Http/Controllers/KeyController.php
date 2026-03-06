<?php

namespace App\Http\Controllers;

use App\Models\Key;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class KeyController extends Controller
{
    public function getIndex(): RedirectResponse
    {
        return redirect('key/list');
    }

    public function getList(): View
    {
        $this->authorize('viewAny', Key::class);

        $keys = Key::all();

        return view('key.list')->with('keys', $keys);
    }

    public function getCreate(): View
    {
        $this->authorize('create', Key::class);

        return view('key.create');
    }

    public function postCreate(): RedirectResponse
    {
        $this->authorize('create', Key::class);

        $rules = [
            'name' => 'required|unique:keys',
            'api_key' => 'required|unique:keys',
        ];

        $validation = Validator::make(Request::all(), $rules);
        if ($validation->fails()) {
            return redirect('key/create')->withErrors($validation->messages());
        }

        $key = new Key;
        $key->name = Request::input('name');
        $key->api_key = Request::input('api_key');
        $key->save();
        Cache::forget('keys');

        return redirect('key/list')->with('success', 'Platform key added!');
    }

    public function getDelete($key_id)
    {
        $key = Key::find($key_id);

        if (empty($key)) {
            return redirect('key/list')->withErrors(['Platform Key not found']);
        }

        $this->authorize('delete', $key);

        return view('key.delete')->with('key', $key);
    }

    public function postDelete($key_id): RedirectResponse
    {
        $key = Key::find($key_id);

        if (empty($key)) {
            return redirect('key/list')->withErrors(['Platform Key not found']);
        }

        $this->authorize('delete', $key);

        $key->delete();
        Cache::forget('keys');

        return redirect('key/list')->with('success', 'Platform Key deleted!');
    }
}
