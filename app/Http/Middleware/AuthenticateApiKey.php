<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Api-Key');

        if (!$key) {
            return response()->json(['message' => 'API Key is missing.'], 401);
        }

      
        $apiKeys = ApiKey::all();
        $validKeyFound = false;

        foreach ($apiKeys as $apiKey) {
            if (hash_equals($apiKey->key, hash('sha256', $key))) {
                $validKeyFound = true;
                break;
            }
        }
        
        if (!$validKeyFound) {
            return response()->json(['message' => 'Invalid API Key.'], 401);
        }
        
        return $next($request);
    }
}