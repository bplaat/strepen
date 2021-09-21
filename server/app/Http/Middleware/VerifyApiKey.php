<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Validator;

class VerifyApiKey
{
    public function handle($request, $next, $checkAuthToken = 'true')
    {
        // Verify API key
        $validation = Validator::make($request->all(), [
            'api_key' => 'required|exists:api_keys,key'
        ]);
        if ($validation->fails()) {
            return response(['errors' => $validation->errors()], 400);
        }

        // Increment API key requests counter
        $apiKey = ApiKey::where('key', request('api_key'))->first();
        $apiKey->requests++;
        $apiKey->save();

        // Check auth token when needed
        if ($checkAuthToken == 'true' && $apiKey->level == ApiKey::LEVEL_REQUIRE_AUTH) {
            return app(Authenticate::class)->handle($request, function ($request) use ($next) {
                return $next($request);
            }, 'sanctum');
        }

        // Go to next middleware
        return $next($request);
    }
}
