<?php

class ModTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        Route::enableFilters();
        
        $user = User::find(1);
        $this->be($user);
    }

    public function testModIndex()
    {
        $this->call('GET', '/mod');

        $this->assertRedirectedTo('/mod/list');
    }

    public function testModCreateGet()
    {
        $this->call('GET', '/mod/create');

        $this->assertResponseOk();
    }

    public function testModDeleteGet()
    {
        $mod = Mod::find(1);

        $this->call('GET', '/mod/delete/'.$mod->id);

        $this->assertResponseOk();
    }

    public function testModList()
    {
        $this->call('GET', '/mod/list');

        $this->assertResponseOk();
    }
}