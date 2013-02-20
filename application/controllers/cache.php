<?php

class Cache_Controller extends Base_Controller {

	public function action_index()
	{
		//
	}

	public function action_update()
	{
		DB::query('DELETE FROM `modpacks`');
		DB::query('DELETE FROM `builds`');

		$modpacks_yml = file_get_contents(Config::get('solder.repo_url').'modpacks.yml');
		$modpacks = Spyc::YAMLLoad($modpacks_yml);

		$modpacks = $modpacks['modpacks'];

		$blocked_packs = array('technicssp','custom1','custom2','custom3');
		foreach ($modpacks as $key => $name)
		{
			if (!in_array($key, $blocked_packs))
			{
				$modpack = new Modpack();
				$modpack->name = $name;
				$modpack->slug = $key;
				$modpack->save();
				if (!$this->getModpack($modpack))
					return Response::json(
						array("error" => "Error grabbing data for " . $modpack->name)
						);
			}
		}

		print_r($modpacks);
		//$checksum = file_get_contents(Config::get('solder.repo_url').'CHECKSUM.md5');
	}

	private function getModpack($modpack)
	{
		try {
			$modpack_yml = file_get_contents(Config::get('solder.repo_url').$modpack->slug.'/'.'modpack.yml');
			$details = Spyc::YAMLLoad($modpack_yml);
			$modpack->url = $details['url'];
			$modpack->recommended = $details['recommended'];
			$modpack->latest = $details['latest'];
			$modpack->icon_md5 = md5_file(Config::get('solder.repo_url').$modpack->slug."/resources/icon.png");
			$modpack->logo_md5 = md5_file(Config::get('solder.repo_url').$modpack->slug."/resources/logo_180.png");
			$modpack->background_md5 = md5_file(Config::get('solder.repo_url').$modpack->slug."/resources/background.jpg");
			$modpack->save();

			if (!$this->getModpackBuilds($modpack,$details))
				return false;

			return true;
		} catch (Exception $e) {
			Log::Exception($e);
		}
		return false;
	}

	private function getModpackBuilds($modpack,$details)
	{
		foreach ($details['builds'] as $version => $data)
		{
			$build = new Build();
			$build->modpack_id = $modpack->id;
			$build->version = $version;
			$build->save();
		}

		return true;
	}

}

?>