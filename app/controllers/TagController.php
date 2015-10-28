<?php

use Illuminate\Support\MessageBag;
class TagController extends BaseController {

    public function __construct()
    {
        parent::__construct();
        $this->beforeFilter('solder_tags');
    }

    public function getIndex()
    {
        return Redirect::to('tag/list');
    }

    public function getList()
    {
        $tags = Tag::all();
        return View::make('tag.list')->with(array('tags' => $tags));
    }

    public function getView($tag_id = null)
    {
        $tag = Tag::find($tag_id);
        if (empty($tag))
            return Redirect::to('tag/list')->withErrors(new MessageBag(array('Tag not found')));

        return View::make('tag.view')->with(array('tag' => $tag));
    }

    public function getCreate()
    {
        return View::make('tag.create');
    }

    public function postCreate()
    {
        $rules = array(
            'name' => 'required|unique:tags',
            'pretty_name' => 'required',
        );
        $messages = array(
            'name.required' => 'You must fill in a tag slug name.',
            'name.unique' => 'The slug you entered is already taken',
            'pretty_name.required' => 'You must enter in a tag name',
        );

        $validation = Validator::make(Input::all(), $rules, $messages);
        if ($validation->fails())
            return Redirect::to('tag/create')->withErrors($validation->messages());

        $tag = new Tag();
        $tag->name = Str::slug(Input::get('name'));
        $tag->pretty_name = Input::get('pretty_name');
        $tag->save();
        return Redirect::to('tag/view/'.$tag->id);
    }

    public function getDelete($tag_id = null)
    {
        $tag = Tag::find($tag_id);
        if (empty($tag))
            return Redirect::to('tag/list')->withErrors(new MessageBag(array('Tag not found')));

        return View::make('tag.delete')->with(array('tag' => $tag));
    }

    public function postModify($tag_id = null)
    {
        $tag = Tag::find($tag_id);
        if (empty($tag))
            return Redirect::to('tag/list')->withErrors(new MessageBag(array('Error modifying tag - Tag not found')));

        $rules = array(
            'pretty_name' => 'required',
            'name' => 'required|unique:tags,name,'.$tag->id,
        );

        $messages = array(
            'name.required' => 'You must fill in a tag slug name.',
            'name.unique' => 'The slug you entered is already in use by another tag',
            'pretty_name.required' => 'You must enter in a tag name',
        );

        $validation = Validator::make(Input::all(), $rules, $messages);
        if ($validation->fails())
            return Redirect::to('tag/view/'.$tag->id)->withErrors($validation->messages());

        $tag->pretty_name = Input::get('pretty_name');
        $tag->name = Input::get('name');
        $tag->save();
        Cache::forget('tag.'.$tag->name);

        return Redirect::to('tag/view/'.$tag->id)->with('success','Tag successfully edited.');
    }

    public function postDelete($tag_id = null)
    {
        $tag = Tag::find($tag_id);
        if (empty($tag))
            return Redirect::to('tag/list')->withErrors(new MessageBag(array('Error deleting tag - Tag not found')));

        $tag->delete();
        Cache::forget('tag.'.$tag->name);

        return Redirect::to('tag/list')->with('success','Tag deleted!');
    }
}
