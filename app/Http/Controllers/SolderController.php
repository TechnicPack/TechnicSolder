<?php namespace App\Http\Controllers;

use App\Libraries\MinecraftUtils;
use App\Libraries\UpdateUtils;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class SolderController extends Controller
{

    public function getConfigure()
    {
        if (Request::has('edit-solder')) {
            Config::set('solder.mirror_url', Request::input('mirror_url'));
            Config::set('solder.repo_location', Request::input('repo_location'));
            Config::set('solder.platform_key', Request::input('platform_key'));
            return redirect('solder/configure')
                ->with('success', 'Your solder configuration has been updated.');
        }
        return view('solder.configure');
    }

    public function getUpdate()
    {
        $rawChangeLog = UpdateUtils::getLatestChangeLog();
        $changelog = array_key_exists('error', $rawChangeLog) ? $rawChangeLog : array_slice($rawChangeLog, 0, 10);
        $latestCommit = array_key_exists('error', $rawChangeLog) ? $rawChangeLog : $rawChangeLog[0];

        $rawLatestVersion = UpdateUtils::getLatestVersion();
        $latestVersion = array_key_exists('error', $rawLatestVersion) ? $rawLatestVersion : $rawLatestVersion['name'];

        $latestData = [
            'version' => $latestVersion,
            'commit' => $latestCommit
        ];

        return view('solder.update')->with('changelog', $changelog)->with('currentVersion', SOLDER_VERSION)->with('latestData', $latestData);
    }

    public function getUpdateCheck()
    {
        if (!Request::ajax()) {
            abort(404);
        }

        if (UpdateUtils::getUpdateCheck()) {
            Cache::put('update', true, now()->addMinutes(60));
            return response()->json([
                'status' => 'success',
                'update' => true
            ]);
        } else {
            if (Cache::get('update')) {
                Cache::forget('update');
            }
            return response()->json([
                'status' => 'success',
                'update' => false
            ]);
        }
    }

    public function getCacheMinecraft()
    {
        if (!Request::ajax()) {
            abort(404);
        }

        $reason = '';
        try {
            $reason = MinecraftUtils::getMinecraft(true);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'reason' => $e->getMessage()
            ]);
        }

        if (Cache::has('minecraftversions')) {
            return response()->json([
                'status' => 'success',
                'reason' => $reason
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'reason' => 'An unknown error has occured.'
            ]);
        }
    }
}