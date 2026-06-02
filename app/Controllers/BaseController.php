<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'text', 'breadcrumb'];

    protected string $locale = 'en';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Detect locale from URL segment (first segment after base URL)
        $uri = $request->getUri();
        $segments = $uri->getSegments();
        $localeFromUrl = $segments[0] ?? '';

        if (in_array($localeFromUrl, ['en', 'hi'])) {
            $this->locale = $localeFromUrl;
        } else {
            // Fallback: check session or default
            $this->locale = session()->get('lang') ?? $this->locale;
        }

        // Set locale for language service
        service('language')->setLocale($this->locale);
        service('request')->setLocale($this->locale);
    }

    /**
     * Get the language prefix for URL generation.
     */
    protected function getLanguagePrefix(): string
    {
        return '/' . $this->locale;
    }

    /**
     * Get the current full URL.
     */
    protected function getCurrentUrl(): string
    {
        return current_url();
    }

    /**
     * Check if the current user is logged in as admin.
     */
    protected function isAdminLoggedIn(): bool
    {
        return session()->has('is_admin_logged_in') && session()->get('is_admin_logged_in') === true;
    }

    /**
     * Check if any user is logged in.
     */
    protected function isLoggedIn(): bool
    {
        return session()->has('user_id');
    }

    /**
     * Get the current user's ID.
     */
    protected function getCurrentUserId(): ?int
    {
        return session()->get('user_id');
    }

    /**
     * Get the current user's role.
     */
    protected function getCurrentUserRole(): ?string
    {
        return session()->get('user_role');
    }
}
