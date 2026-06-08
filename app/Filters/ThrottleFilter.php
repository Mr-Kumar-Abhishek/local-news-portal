<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ThrottleFilter - Simple Rate Limiting
 *
 * Limits requests per IP address to prevent abuse.
 * Default: 60 requests per minute per IP.
 *
 * Uses CodeIgniter's cache service for storage.
 */
class ThrottleFilter implements FilterInterface
{
    /**
     * Maximum requests allowed per minute per IP.
     */
    protected int $maxRequests = 60;

    /**
     * Time window in seconds (1 minute).
     */
    protected int $timeWindow = 60;

    /**
     * Routes excluded from throttling.
     */
    protected array $exceptRoutes = [
        'admin',
        'login',
        'register',
        'forgot-password',
        'reset-password',
    ];

    /**
     * Executed before the controller method.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Allow custom rate limit from arguments
        if (! empty($arguments) && is_numeric($arguments[0])) {
            $this->maxRequests = (int) $arguments[0];
        }

        // Skip throttling for admin and auth routes
        $currentPath = $request->getUri()->getPath();
        foreach ($this->exceptRoutes as $excluded) {
            if (strpos($currentPath, $excluded) !== false) {
                return;
            }
        }

        // Get client IP
        $ip = $request->getIPAddress();

        // Build cache key: throttle:{ip}:{minute_window}
        $minuteWindow = (int) floor(time() / $this->timeWindow);
        $cacheKey = 'throttle:' . md5($ip) . ':' . $minuteWindow;

        $cache = service('cache');
        $requestCount = (int) $cache->get($cacheKey);

        if ($requestCount >= $this->maxRequests) {
            return service('response')
                ->setStatusCode(429)
                ->setJSON([
                    'status'  => 429,
                    'error'   => 'Too Many Requests',
                    'message' => 'Rate limit exceeded. Please try again later.',
                    'retry_after' => $this->timeWindow,
                ]);
        }

        // Increment the counter
        $cache->save($cacheKey, $requestCount + 1, $this->timeWindow);
    }

    /**
     * Executed after the controller method.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the request
    }
}
