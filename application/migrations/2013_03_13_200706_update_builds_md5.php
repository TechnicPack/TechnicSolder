<?php

class Update_Builds_Md5 {

	const MINECRAFT_API = 'http://www.technicpack.net/api/minecraft';

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('builds', function($table) {
			$table->string('minecraft_md5');
		});

		$minecraft = $this->getMinecraft();
		$minecraft = (Array)$minecraft;

		foreach (Build::all() as $build)
		{
			$build->minecraft_md5 = $minecraft[$build->minecraft]->md5;
			$build->save();
		}
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('builds', function($table) {
			$table->drop_column('minecraft_md5');
		});
	}

	public function getMinecraft()
	{
		if (Config::has('solder.minecraft_api'))
		{
			$url = Config::get('solder.minecraft_api');
		} else {
			$url = self::MINECRAFT_API;
		}

		$response = UrlUtils::get_url_contents($url);

		return json_decode($response);
	}
}