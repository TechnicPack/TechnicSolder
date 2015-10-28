<?php

class TagTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        Route::enableFilters();

        $user = User::find(1);
        $this->be($user);
    }

    public function testTagIndex()
    {
        $this->call('GET', '/tag');

        $this->assertRedirectedTo('/tag/list');
    }

    public function testTagList()
    {
        $this->call('GET', '/tag/list');

        $this->assertResponseOk();
    }

    public function testTagCreateGet()
    {
        $this->call('GET', '/tag/create');

        $this->assertResponseOk();
    }

    public function testTagCreatePostNonUniqueName()
    {
        $data = array(
            'pretty_name' => 'TestTag',
            'name' => 'testtag'
        );

        $response = $this->call('POST', '/tag/create', $data);
        $this->assertRedirectedTo('/tag/create');
        $this->assertSessionHasErrors('name');
    }

    public function testTagCreatePost()
    {
        $data = array(
            'pretty_name' => 'Client',
            'name' => 'client',
        );

        $response = $this->call('POST', '/tag/create', $data);
        $this->assertRedirectedTo('/tag/view/2');
    }

    public function testTagDeleteGet()
    {
        $tag = Tag::find(1);

        $this->call('GET', '/tag/delete/'.$tag->id);

        $this->assertResponseOk();
    }

    public function testTagDeleteGetInvalidID()
    {
        $this->call('GET', '/tag/delete/100000');
        $this->assertRedirectedTo('/tag/list');
    }

    public function testTagDeletePostInvalidID()
    {
        $this->call('POST', '/tag/delete/100000');
        $this->assertRedirectedTo('/tag/list');
    }

    public function testTagDeletePost()
    {
        $tag = Tag::where('name', '=', 'client')->firstOrFail();

        $this->call('POST', '/tag/delete/'.$tag->id);
        $this->assertRedirectedTo('/tag/list');
        $this->assertSessionHas('success');
    }
}
