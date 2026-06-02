<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'text', 'html'];

    protected string $locale = 'en';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->locale = service('request')->getLocale() ?? 'en';
        service('language')->setLocale($this->locale);
    }

    protected function isAdminLoggedIn(): bool
    {
        return session()->has('is_admin_logged_in') && session()->get('is_admin_logged_in') === true;
    }

    protected function getCurrentUserId(): ?int
    {
        return session()->get('user_id');
    }

    protected function getCurrentUserRole(): ?string
    {
        return session()->get('user_role');
    }

    protected function getCurrentUserName(): ?string
    {
        return session()->get('user_full_name');
    }
}
