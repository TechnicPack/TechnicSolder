<?php

class TagTableTestSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('tags')->delete();

        Tag::create(
            array(
                'pretty_name' => 'TestTag',
                'name'        => 'testtag',
            )
        );
    }

}
