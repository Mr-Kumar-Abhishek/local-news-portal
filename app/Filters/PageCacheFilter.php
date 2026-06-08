<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PageCacheFilter - Simple Page Caching
 *
 * Caches full page responses for GET requests on public pages.
 * Excludes admin, auth, and POST requests.
 *
 * Cache key format: pagecache:{locale}:{url}
 * Default TTL: 1 hour (3600 seconds)
 */
class PageCacheFilter implements FilterInterface
{
    /**
     * Cache TTL in seconds.
     */
    protected int $ttl = 3600;

    /**
     * Routes excluded from caching.
     */
    protected array $excludedSegments = [
        'admin',
        'login',
        'register',
        'forgot-password',
        'reset-password',
        'logout',
        'search',
        'rss',
        'sitemap',
    ];

    /**
     * Executed before the controller method.
     * Check if a cached version of the current page exists and serve it.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Allow custom TTL from arguments
        if (! empty($arguments) && is_numeric($arguments[0])) {
            $this->ttl = (int) $arguments[0];
        }

        // Only cache GET requests
        if ($request->getMethod() !== 'GET') {
            return;
        }

        // Skip caching for excluded routes
        $currentPath = $request->getUri()->getPath();
        foreach ($this->excludedSegments as $segment) {
            if (strpos($currentPath, $segment) !== false) {
                return;
            }
        }

        // Check if the user is logged in (don't cache authenticated pages)
        if (session()->has('is_logged_in') && session()->get('is_logged_in') === true) {
            return;
        }

        // Build cache key
        $cacheKey = $this->buildCacheKey($request);

        $cache = service('cache');
        $cachedResponse = $cache->get($cacheKey);

        if ($cachedResponse !== null) {
            // Serve the cached response
            $response = service('response');
            $response->setBody($cachedResponse['body']);

            if (! empty($cachedResponse['headers'])) {
                foreach ($cachedResponse['headers'] as $name => $value) {
                    $response->setHeader($name, $value);
                }
            }

            // Add a header indicating cache hit
            $response->setHeader('X-Page-Cache', 'HIT');

            return $response;
        }

        // Mark request for caching in after()
        $request->pageCacheKey = $cacheKey;
    }

    /**
     * Executed after the controller method.
     * Cache the response if appropriate.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Only cache if we have a cache key (set in before())
        if (empty($request->pageCacheKey)) {
            return;
        }

        // Only cache successful responses (2xx)
        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            return;
        }

        $cacheData = [
            'body'    => $response->getBody(),
            'headers' => [
                'Content-Type' => $response->getHeaderLine('Content-Type') ?: 'text/html; charset=UTF-8',
            ],
            'cached_at' => time(),
        ];

        $cache = service('cache');
        $cache->save($request->pageCacheKey, $cacheData, $this->ttl);

        // Add a header indicating cache miss (will be stored)
        $response->setHeader('X-Page-Cache', 'MISS');
    }

    /**
     * Build a unique cache key for the current request.
     */
    protected function buildCacheKey(RequestInterface $request): string
    {
        $url = $request->getUri()->getPath();

        // Include query string for pagination, etc. (but exclude random params)
        $queryParams = $request->getGet();
        if (! empty($queryParams)) {
            // Only include specific query params relevant for caching
            $allowedParams = ['page', 'perPage', 'p', 'limit'];
            $filteredParams = array_intersect_key($queryParams, array_flip($allowedParams));
            if (! empty($filteredParams)) {
                ksort($filteredParams);
                $url .= '?' . http_build_query($filteredParams);
            }
        }

        // Normalize URL (remove trailing slash, lowercase)
        $url = rtrim(strtolower($url), '/');

        return 'pagecache:' . md5($url);
    }
}
