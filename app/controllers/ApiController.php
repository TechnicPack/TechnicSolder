<?php


/**
* API Controller
*/
class ApiController extends BaseController
{

	/**
	 * Main API Controller Index
	 * @return String JSON Response
	 */
	public function getIndex()
	{
		return Response::json(array(
				'api'     => 'TechnicSolder', 
				'version' => $this->getSolderVersion(),
				'stream' => $this->getSolderStream()
				));
	}
	
	/**
	 * Retrieve a Modpack
	 * @param  String $modpack Modpack slug
	 * @param  String $build   Modpack build number
	 * @return String          JSON Response
	 */
	public function getModpack($modpack = null, $build = null)
	{
		if (empty($modpack))
			return Response::json($this->fetchModpacks());
		else {
			if (empty($build))
				return Response::json($this->fetchModpack($modpack));
			else
				return Response::json($this->fetchBuild($modpack, $build));
		}
	}

	/**
	 * Retrieve a specific Mod
	 * @param  String $mod     Mod Slug
	 * @param  String $version Mod Version
	 * @return String          JSON Response
	 */
	public function getMod($mod = null, $version = null)
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
			return Response::json($this->fetchMod($mod));

		return Response::json($this->fetchModversion($mod,$version));
	}

	/**
	 * Verify Platform API Key
	 * @param  String $key API Key
	 * @return String      JSON Response
	 */
	public function getVerify($key = null)
	{
		if (empty($key))
			return Response::json(array("error" => "No API key provided."));

		if ($key == Config::get('solder.platform_key'))
			return Response::json(array("valid" => "Key validated."));
		else
			return Response::json(array("error" => "Invalid key provided."));
	}

	/**
	 * Fetch All Modpacks
	 * @return Array Array containing all available modpacks
	 */
	private function fetchModpacks()
	{
		if (Cache::has('modpacks'))
		{
			$modpacks = Cache::get('modpacks');
		} else {
			$modpacks = Modpack::all();
			Cache::put('modpacks', $modpacks, 5);
		}

		$response = array();
		$response['modpacks'] = array();
		foreach ($modpacks as $modpack)
		{
			$response['modpacks'][$modpack->slug] = $modpack->name;
		}

		$response['mirror_url'] = Config::get('solder.mirror_url');

		return $response;
	}

	/**
	 * Fetch Specific Modpack
	 * @param  String $slug Modpack Slug
	 * @return Array       Formatted Array with modpack data
	 */
	private function fetchModpack($slug)
	{
		$response = array();

		if (Cache::has('modpack.'.$slug))
		{
			$modpack = Cache::Get('modpack.'.$slug);
		} else {
			$modpack = Modpack::where("slug","=",$slug)->first();
			Cache::put('modpack.'.$slug,$modpack,5);
		}
		

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
			if ($build->is_published)
				array_push($response['builds'], $build->version);
		}

		return $response;
	}

	/**
	 * Fetch Specific Modpack Build
	 * @param  String $slug  Modpack Slug
	 * @param  String $build Modpack Build Number
	 * @return Array        Array containing build information and contained mods
	 */
	private function fetchBuild($slug, $build)
	{
		$response = array();

		if (Cache::has('modpack.'.$slug))
		{
			$modpack = Cache::Get('modpack.'.$slug);
		} else {
			$modpack = Modpack::where("slug","=",$slug)->first();
			Cache::put('modpack.'.$slug,$modpack,5);
		}

		if (empty($modpack))
			return array("error" => "Modpack does not exist");
			
		$buildpass = $build;
		if (Cache::has('modpack.'.$slug.'.build.'.$build))
		{
			$build = Cache::get('modpack.'.$slug.'.build.'.$build);
		} else {
			$build = Build::with('modversions')
						->where("modpack_id", "=", $modpack->id)
						->where("version", "=", $build)->first();
			Cache::put('modpack.'.$slug.'.build.'.$buildpass,$build,5);
		}

		if (empty($build))
			return array("error" => "Build does not exist");

		$response['minecraft'] = $build->minecraft;
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

	/**
	 * Fetch Specific Mod
	 * @param  String $mod Mod Slug
	 * @return Array      Array containing mod information
	 */
	private function fetchMod($mod)
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

	/**
	 * Fetch Specific Mod Version
	 * @param  String $mod     Mod Slug
	 * @param  String $version Mod Version
	 * @return Array          Array containing mod version information
	 */
	private function fetchModversion($mod, $version)
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


}

?>