<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        return redirect()->route('login')->with('status', 'You have been logged out.');
    }
}
