<?php

class Cache_Controller extends Base_Controller {

	public function action_index()
	{
		//
	}

	public function action_update()
	{
		$this->clearDatabase();

		$this->getMods();

		$this->getModpacks();

		return Response::json(array("success" => "Repoistory was successfully updated"));
	}

	private function clearDatabase()
	{
		DB::query('DELETE FROM `modpacks`');
		DB::query('DELETE FROM `builds`');
		DB::query('DELETE FROM `mods`');
		DB::query('DELETE FROM `modversions`');
		DB::query('DELETE FROM `build_modversion`');
	}

	private function getMods()
	{
		$library_yml = file_get_contents(Config::get('solder.repo_url').'modlibrary.yml');
		$library = Spyc::YAMLLoad($library_yml);

		foreach ($library['mods'] as $name => $data)
		{
			try {
				$mod = new Mod();
				$mod->name = $name;
				if (isset($data['description']))
					$mod->description = $data['description'];
				if (isset($data['link']))
					$mod->link = $data['link'];
				if (isset($data['author']))
					$mod->author = $data['author'];
				$mod->save();

				$this->getModVersions($mod,$data['versions']);
			} catch (Exception $e) {
				Log::exception($e);
			}
		}
	}

	private function getModVersions($mod, $versions)
	{
		foreach ($versions as $version => $data)
		{
			try {
				$ver = new ModVersion();
				$ver->mod_id = $mod->id;
				$ver->version = $version;
				$ver->save();
			} catch (Exception $e) {
				Log::exception($e);
			}
		}
	}

	private function getModpacks()
	{
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
	}

	private function getModpack($modpack)
	{
		try {
			$modpack_yml = file_get_contents(Config::get('solder.repo_url').$modpack->slug.'/'.'modpack.yml');
			$details     = Spyc::YAMLLoad($modpack_yml);

			$modpack->url            = $details['url'];
			$modpack->recommended    = $details['recommended'];
			$modpack->latest         = $details['latest'];
			$modpack->icon_md5       = md5_file(Config::get('solder.repo_url').$modpack->slug."/resources/icon.png");
			$modpack->logo_md5       = md5_file(Config::get('solder.repo_url').$modpack->slug."/resources/logo_180.png");
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
			$build->minecraft = $data['minecraft'];
			if (isset($data['forge']))
				$build->forge = $data['forge'];
			$build->save();

			if (!$this->getBuildMods($build, $data['mods']))
				return false;
		}

		return true;
	}

	private function getBuildMods($build, $mods)
	{
		$version_ids = array();
		foreach ($mods as $name => $version)
		{
			$mod = Mod::where('name', '=', $name)->first();
			$version = ModVersion::where('mod_id', '=', $mod->id)->where('version', '=', $version)->first();
			if (!empty($version))
				array_push($version_ids, $version->id);
			else {
				echo $build->modpack->name . " - Failed on mod " . $name . " Version: ". $version ."<br />";
			}
		}
		$build->modversions()->sync($version_ids);

		return true;
	}

}

?>