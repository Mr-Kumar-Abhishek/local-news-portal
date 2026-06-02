<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->has('is_logged_in') || session()->get('is_logged_in') !== true) {
            return redirect()->to('/' . $request->getLocale() . '/login')
                           ->with('error', 'Please login first');
        }

        if (!session()->has('is_admin_logged_in') || session()->get('is_admin_logged_in') !== true) {
            return redirect()->to('/')
                           ->with('error', 'Access denied. Admin privileges required.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed here
    }
}
