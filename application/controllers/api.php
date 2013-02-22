<?php

class API_Controller extends Base_Controller {
	public $restful = true;

	public function get_index()
	{
		return Response::json(array(
				'api'     => 'TechnicSolder', 
				'version' => SOLDER_VERSION
				));
	}

	public function get_modpack($modpack = null, $build = null)
	{
		if (empty($modpack))
			return Response::json($this->fetch_modpacks());
		else {
			if (empty($build))
				return Response::json($this->fetch_modpack($modpack));
			else
				return Response::json($this->fetch_build($modpack, $build));
		}
	}

	public function get_mod($mod = null, $version = null)
	{
		if (empty($mod))
			return Response::json(array("error" => "No mod requested"));

		$mod = Mod::where('name', '=', $mod)->first();

		if (empty($mod))
			return Response::json(array('error' => 'Mod does not exist'));

		if (empty($version))
			return Response::json($this->fetch_mod($mod));

		return Response::json($this->fetch_modversion($mod,$version));
	}

	public function get_verify($key = null)
	{
		if (empty($key))
			return Response::json(array("error" => "No API key provided."));

		if ($key == Config::get('solder.platform_key'))
			return Response::json(array("valid" => "Key validated."));
		else
			return Response::json(array("error" => "Invalid key provided."));
	}


	/* Private Functions */

	private function fetch_mod($mod)
	{
		$response = array();

		$response['name'] = $mod->name;
		$response['author'] = $mod->author;
		$response['description'] = $mod->description;
		$response['link'] = $mod->link;
		$response['versions'] = array();

		foreach ($mod->versions as $version)
		{
			array_push($response['versions'], $version->version);
		}

		return $response;
	}

	private function fetch_modversion($mod, $version)
	{
		$response = array();

		$version = ModVersion::where("mod_id", "=", $mod->id)
								->where("version", "=", $version)->first();

		if (empty($version))
			return array("error" => "Mod version does not exist");

		$response['md5'] = $version->md5;

		return $response;
	}

	private function fetch_modpacks()
	{
		$modpacks = Modpack::all();

		$response = array();
		$response['modpacks'] = array();
		foreach ($modpacks as $modpack)
		{
			$response['modpacks'][$modpack->slug] = $modpack->name;
		}

		$response['mirror_url'] = Config::get('solder.repo_url');

		return $response;
	}

	private function fetch_modpack($slug)
	{
		$response = array();

		$modpack = Modpack::where("slug","=",$slug)->first();

		if (empty($modpack))
			return array("error" => "Modpack does not exist");

		$response['name']           = $modpack->slug;
		$response['url']            = $modpack->url;
		$response['icon_md5']       = $modpack->icon_md5;
		$response['logo_md5']       = $modpack->logo_md5;
		$response['background_md5'] = $modpack->background_md5;
		$response['recommended']    = $modpack->recommended;
		$response['latest']         = $modpack->latest;
		$response['builds']         = array();

		foreach ($modpack->builds as $build)
		{
			array_push($response['builds'], $build->version);
		}

		return $response;
	}

	private function fetch_build($slug, $build)
	{
		$response = array();

		$modpack = Modpack::where("slug","=",$slug)->first();

		if (empty($modpack))
			return array("error" => "Modpack does not exist");

		$build = Build::where("modpack_id", "=", $modpack->id)
						->where("version", "=", $build)->first();

		if (empty($build))
			return array("error" => "Build does not exist");

		$response['minecraft'] = $build->minecraft;
		$response['forge'] = $build->forge;
		$response['mods'] = array();

		foreach ($build->modversions as $modversion)
		{
			$response['mods'][$modversion->mod->name] = $modversion->version;
		}

		return $response;
	}

}

?>