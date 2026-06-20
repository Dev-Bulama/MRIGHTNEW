<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get API key from multiple possible sources
        $apiKey = $this->getApiKeyFromRequest($request);

        if (!$apiKey) {
            Log::warning('AntiTheft API: Missing API key', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'API key is required',
                'error' => 'Missing authentication credentials',
                'documentation' => 'Please include your API key in the Authorization header as "Bearer YOUR_API_KEY" or in the X-API-Key header',
            ], 401);
        }

        // Validate the API key
        if (!$this->isValidApiKey($apiKey)) {
            Log::warning('AntiTheft API: Invalid API key attempted', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'provided_key' => substr($apiKey, 0, 8) . '...', // Log only first 8 chars for security
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
                'error' => 'Authentication failed',
                'documentation' => 'Please verify your API key is correct and has not expired',
            ], 401);
        }

        // Log successful authentication
        Log::info('AntiTheft API: Successful authentication', [
            'ip' => $request->ip(),
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'key_used' => substr($apiKey, 0, 8) . '...',
        ]);

        // Add API key info to request for later use
        $request->attributes->add([
            'api_key' => $apiKey,
            'api_authenticated' => true,
            'api_auth_time' => now(),
        ]);

        return $next($request);
    }

    /**
     * Get API key from request headers or query parameters
     */
    private function getApiKeyFromRequest(Request $request): ?string
    {
        // Check Authorization header (Bearer token)
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Check X-API-Key header
        $apiKeyHeader = $request->header('X-API-Key');
        if ($apiKeyHeader) {
            return $apiKeyHeader;
        }

        // Check query parameter (less secure, but for testing)
        $apiKeyQuery = $request->query('api_key');
        if ($apiKeyQuery) {
            return $apiKeyQuery;
        }

        return null;
    }

    /**
     * Validate the provided API key
     */
    private function isValidApiKey(string $apiKey): bool
    {
        // Get valid API keys from configuration
        $validKeys = $this->getValidApiKeys();

        return in_array($apiKey, $validKeys, true);
    }

    /**
     * Get list of valid API keys
     */
//      private function getValidApiKeys(): array
// {
//     // HARDCODED for hPanel (temporary fix)
//     $hardcodedKeys = [
//         'mright-prod-695310844',
//         'mright-backup-1710314338',
//         'mright-emergency-1093529146',
//         'mright-dev-key-2025',
//     ];
    
//     // Try to get from config first
//     $configKeys = config('services.antitheft.api_keys', []);
    
//     // Return hardcoded if config is empty
//     if (empty($configKeys)) {
//         return $hardcodedKeys;
//     }
    
//     return array_merge($hardcodedKeys, $configKeys);
//}
    private function getValidApiKeys(): array
    {
        // Primary method: Get from environment configuration
        $envKeys = config('services.antitheft.api_keys', []);
        
        // Secondary method: Get from single API key config
        $singleKey = config('services.antitheft.api_key');
        
        // Combine all valid keys
        $validKeys = [];
        
        if (is_array($envKeys)) {
            $validKeys = array_merge($validKeys, $envKeys);
        }
        
        if ($singleKey) {
            $validKeys[] = $singleKey;
        }

        // Default development keys (only if no other keys are configured)
        if (empty($validKeys) && app()->environment('local', 'development')) {
            $validKeys = [
                'mright-dev-key-2025',
                'antitheft-dev-access-token',
            ];
        }

        // Production fallback key (should be set in environment)
        if (empty($validKeys)) {
            $validKeys = [
                config('app.key'), // Use app key as fallback
            ];
        }

        return array_filter($validKeys); // Remove empty values
    }

    /**
     * Get rate limiting key for the authenticated API key
     */
    public static function getRateLimitKey(Request $request): string
    {
        $apiKey = $request->attributes->get('api_key', 'unknown');
        return 'api_rate_limit:' . hash('sha256', $apiKey);
    }
}