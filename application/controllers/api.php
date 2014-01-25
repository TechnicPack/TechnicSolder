<?php

class API_Controller extends Base_Controller {
	public $restful = true;
	public $client = null;

	public function __construct()
	{
		parent::__construct();

		/* This checks the client list for the CID. If a matching CID is found, all caching will be ignored
		   for this request */

		if (Cache::has('clients'))
			$clients = Cache::get('clients');
		else {
			$clients = Client::all();
			Cache::put('clients', $clients, 1);
		}

		foreach ($clients as $client) {
			if ($client->uuid == Input::get('cid')) {
				$this->client = $client;
			}
		}

	}

	public function get_index()
	{
		return Response::json(array(
				'api'     => 'TechnicSolder', 
				'version' => SOLDER_VERSION,
				'stream' => SOLDER_STREAM
				));
	}

	public function get_modpack($modpack = null, $build = null)
	{
		if (empty($modpack))
		{
			if (Input::has('include'))
			{
				$include = Input::get('include');
				switch ($include)
				{
					case "full":
						$modpacks = $this->fetch_modpacks();
						$m_array = array();
						foreach ($modpacks['modpacks'] as $slug => $name)
						{
							$modpack = $this->fetch_modpack($slug);
							$m_array[$slug] = $modpack;
						}
						$response = array();
						$response['modpacks'] = $m_array;
						$response['mirror_url'] = $modpacks['mirror_url'];
						return Response::json($response);
						break;
				}
			} else {
				return Response::json($this->fetch_modpacks());
			}
		}
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

		if (Cache::has('mod.'.$mod))
		{
			$mod = Cache::get('mod.'.$mod);
		} else {
			$modname = $mod;
			$mod = Mod::where('name', '=', $mod)->first();
			Cache::put('mod.'.$modname,$mod,5);
		}

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

		$key = Key::where('api_key', '=', $key)->first();

		if (empty($key))
			return Response::json(array("error" => "Invalid key provided."));
		else
			return Response::json(array("valid" => "Key validated.", "name" => $key->name, "created_at" => $key->created_at));
	}


	/* Private Functions */

	private function fetch_mod($mod)
	{
		$response = array();

		$response['name'] = $mod->name;
		$response['pretty_name'] = $mod->pretty_name;
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
		$response['url'] = Config::get('solder.mirror_url').'mods/'.$version->mod->name.'/'.$version->mod->name.'-'.$version->version.'.zip';

		return $response;
	}

	private function fetch_modpacks()
	{
		if (Cache::has('modpacks') && empty($this->client))
		{
			$modpacks = Cache::get('modpacks');
		} else {
			$modpacks = Modpack::where('hidden','=','0')->order_by('order')->get();
			if (empty($this->client)) {
				Cache::put('modpacks', $modpacks, 5);
			}
			
		}

		$response = array();
		$response['modpacks'] = array();
		foreach ($modpacks as $modpack)
		{
			if ($modpack->private == 1) {
				if (isset($this->client)) {
					foreach ($this->client->modpacks as $pmodpack) {
						if ($pmodpack->id == $modpack->id) {
							$response['modpacks'][$modpack->slug] = $modpack->name;
						}
					}
				}
			} else {
				$response['modpacks'][$modpack->slug] = $modpack->name;
			}
		}

		$response['mirror_url'] = Config::get('solder.mirror_url');

		return $response;
	}

	private function fetch_modpack($slug)
	{
		$response = array();

		if (Cache::has('modpack.'.$slug) && empty($this->client))
		{
			$modpack = Cache::get('modpack.'.$slug);
		} else {
			$modpack = Modpack::with('Builds')
							->where("slug","=",$slug)->first();
			if (empty($this->client))
				Cache::put('modpack.'.$slug,$modpack,5);
		}
		

		if (empty($modpack))
			return array("error" => "Modpack does not exist");

		$response['name']           = $modpack->slug;
		$response['display_name']	= $modpack->name;
		$response['url']            = $modpack->url;

		$resourcePath = URL::to_asset('resources/' . $modpack->slug);

		if ($modpack->icon == 0 && !empty($modpack->icon_md5)) {
			$response['icon'] = Config::get('solder.mirror_url') . $modpack->slug . "/resources/icon.png";
		} else if ($modpack->icon == 0 && empty($modpack->icon_md5)) {
			$response['icon'] = URL::to_asset('resources/default/icon.png');
			$modpack->icon_md5 = md5_file(path('public') . 'resources/default/icon.png');
		} else {
			if (Config::get('solder.use_s3')) {
				$response['icon'] = Config::get('solder.s3_url').'resources/'.$modpack->slug.'/icon.png?'.TimeUtils::getTimestampDate($modpack->updated_at);
			} else {
				$response['icon'] = $resourcePath . "/icon.png";
			}
		}

		$response['icon_md5']       = $modpack->icon_md5;

		if ($modpack->logo == 0 && !empty($modpack->logo_md5)) {
			$response['logo'] = Config::get('solder.mirror_url') . $modpack->slug . "/resources/logo.png";
		} else if ($modpack->logo == 0 && empty($modpack->logo_md5)) {
			$response['logo'] = URL::to_asset('resources/default/logo.png');
			$modpack->logo_md5 = md5_file(path('public') . 'resources/default/logo.png');
		} else {
			if (Config::get('solder.use_s3')) {
				$response['logo'] = Config::get('solder.s3_url').'resources/'.$modpack->slug.'/logo.png?'.TimeUtils::getTimestampDate($modpack->updated_at);
			} else {
				$response['logo'] = $resourcePath . "/logo.png";
			}
		}

		$response['logo_md5']       = $modpack->logo_md5;

		if ($modpack->background == 0 && !empty($modpack->background_md5)) {
			$response['background'] = Config::get('solder.mirror_url') . $modpack->slug . "/resources/background.png";
		} else if ($modpack->background == 0 && empty($modpack->background_md5)) {
			$response['background'] = URL::to_asset('resources/default/background.png');
			$modpack->background_md5 = md5_file(path('public') . 'resources/default/background.png');
		} else {
			if (Config::get('solder.use_s3')) {
				$response['background'] = Config::get('solder.s3_url').'resources/'.$modpack->slug.'/background.png?'.TimeUtils::getTimestampDate($modpack->updated_at);
			} else {
				$response['background'] = $resourcePath . "/background.png";
			}
		}

		$response['background_md5'] = $modpack->background_md5;
		$response['recommended']    = $modpack->recommended;
		$response['latest']         = $modpack->latest;
		$response['builds']         = array();

		foreach ($modpack->builds as $build)
		{
			if ($build->is_published) {
				if (!$build->private) {
					array_push($response['builds'], $build->version);
				} else if (isset($this->client)) {
					foreach ($this->client->modpacks as $pmodpack) {
						if ($modpack->id == $pmodpack->id) {
							array_push($response['builds'], $build->version);
						}
					}
				}
			}
		}

		return $response;
	}

	private function fetch_build($slug, $build)
	{
		$response = array();

		if (Cache::has('modpack.'.$slug) && empty($this->client))
		{
			$modpack = Cache::Get('modpack.'.$slug);
		} else {
			$modpack = Modpack::where("slug","=",$slug)->first();
			if (empty($this->client))
				Cache::put('modpack.'.$slug,$modpack,5);
		}

		if (empty($modpack))
			return array("error" => "Modpack does not exist");
			
		$buildpass = $build;
		if (Cache::has('modpack.'.$slug.'.build.'.$build) && empty($this->client))
		{
			$build = Cache::get('modpack.'.$slug.'.build.'.$build);
		} else {
			$build = Build::with('ModVersions')
						->where("modpack_id", "=", $modpack->id)
						->where("version", "=", $build)->first();
			if (empty($this->client))
				Cache::put('modpack.'.$slug.'.build.'.$buildpass,$build,5);
		}

		if (empty($build))
			return array("error" => "Build does not exist");

		$response['minecraft'] = $build->minecraft;
		$response['minecraft_md5'] = $build->minecraft_md5;
		$response['forge'] = $build->forge;
		$response['mods'] = array();

		foreach ($build->modversions as $modversion)
		{
			if (!Input::has('include'))
			{
				$response['mods'][] = array(
											"name" => $modversion->mod->name,
											"version" => $modversion->version,
											"md5" => $modversion->md5,
											"url" => Config::get('solder.mirror_url').'mods/'.$modversion->mod->name.'/'.$modversion->mod->name.'-'.$modversion->version.'.zip'
											);
			} else if (Input::get('include') == "mods") {
				$response['mods'][] = array(
											"name" => $modversion->mod->name,
											"version" => $modversion->version,
											"md5" => $modversion->md5,
											"pretty_name" => $modversion->mod->pretty_name,
											"author" => $modversion->mod->author,
											"description" => $modversion->mod->description,
											"link" => $modversion->mod->link
											);
			} else {
				$data = array(
											"name" => $modversion->mod->name,
											"version" => $modversion->version,
											"md5" => $modversion->md5,
											);
				$request = explode(",", Input::get('include'));
				$mod = (array)$modversion->mod;
				$mod = $mod['attributes'];
				foreach ($request as $type)
				{
					if (isset($mod[$type]))
						$data[$type] = $mod[$type];
				}

				$response['mods'][] = $data;
			}
			
		}

		return $response;
	}

}

?>