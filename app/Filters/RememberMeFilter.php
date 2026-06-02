<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\UserModel;

class RememberMeFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only run if the user is not already logged in
        if (!session()->has('user_id')) {
            helper('cookie');
            $cookie = get_cookie('remember_me');

            if ($cookie) {
                $parts = explode(':', $cookie);
                if (count($parts) === 2) {
                    [$userId, $token] = $parts;
                    
                    $userModel = new UserModel();
                    $user = $userModel->find($userId);

                    if ($user && $user->status == 1 && !empty($user->remember_token) && !empty($user->remember_expires_at)) {
                        $hashedToken = hash('sha256', $token);
                        
                        $expires = \CodeIgniter\I18n\Time::parse($user->remember_expires_at);
                        if (hash_equals($user->remember_token, $hashedToken) && $expires->isAfter(\CodeIgniter\I18n\Time::now())) {
                            // Log user in
                            session()->set([
                                'user_id'            => $user->id,
                                'user_role'          => $user->role,
                                'user_full_name'     => $user->full_name,
                                'user_language'      => $user->language_preference,
                                'is_admin_logged_in' => in_array($user->role, ['admin', 'editor', 'journalist']),
                                'is_logged_in'       => true,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
