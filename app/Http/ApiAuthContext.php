<?php

namespace App\Http;

use App\Models\Client;
use App\Models\Key;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

readonly class ApiAuthContext
{
    public function __construct(
        public ?Client $client,
        public ?Key $key,
        public ?User $user,
    ) {}

    public static function fromRequest(): self
    {
        $clients = Cache::remember('clients', now()->addMinutes(1), fn () => Client::all());
        $keys = Cache::remember('keys', now()->addMinutes(1), fn () => Key::all());

        $inputClientId = Request::input('cid');
        $client = $inputClientId ? $clients->firstWhere('uuid', '===', $inputClientId) : null;
        $client?->load('modpacks');

        $inputKey = Request::input('k');
        $key = $inputKey ? $keys->firstWhere('api_key', '===', $inputKey) : null;

        $authUser = auth('sanctum')->user();
        $user = $authUser instanceof User ? $authUser : null;

        return new self($client, $key, $user);
    }
}
