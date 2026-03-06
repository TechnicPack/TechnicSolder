<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\MinecraftUtils;
use Illuminate\Http\JsonResponse;

class MinecraftController extends Controller
{
    public function refresh(): JsonResponse
    {
        MinecraftUtils::getMinecraftVersions(true);

        return response()->json(['success' => 'Minecraft versions cache refreshed.']);
    }
}
