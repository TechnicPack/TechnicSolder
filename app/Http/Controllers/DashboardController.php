<?php

namespace App\Http\Controllers;

use App\Libraries\UpdateUtils;
use App\Models\Build;
use App\Models\Modversion;

class DashboardController extends Controller
{
    public function getIndex()
    {
        $builds = Build::with('modpack')
            ->withCount('modversions')
            ->where('is_published', '=', '1')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $modversions = Modversion::with('mod')
            ->whereNotNull('md5')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $rawChangeLog = UpdateUtils::getLatestChangeLog();
        $changelogJson = array_key_exists('error', $rawChangeLog) ? $rawChangeLog : array_slice($rawChangeLog, 0, 10);

        return view('dashboard.index')
            ->with('modversions', $modversions)
            ->with('builds', $builds)
            ->with('changelog', $changelogJson);
    }
}
