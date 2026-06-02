<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/' . $this->locale . '/');
        }

        $data = [
            'locale' => $this->locale,
            'title'  => lang('News.login'),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return view('templates/header', $data)
                     . view('auth/login', ['validation' => $this->validator, 'locale' => $this->locale])
                     . view('templates/footer');
            }

            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $user = $this->userModel->attemptLogin($email, $password);

            if ($user) {
                session()->set([
                    'user_id'            => $user->id,
                    'user_role'          => $user->role,
                    'user_full_name'     => $user->full_name,
                    'user_language'      => $user->language_preference,
                    'is_admin_logged_in' => in_array($user->role, ['admin', 'editor', 'journalist']),
                    'is_logged_in'       => true,
                ]);

                $response = redirect()->to('/' . $this->locale . '/admin/dashboard');

                if ($this->request->getPost('remember') !== null) {
                    $token = bin2hex(random_bytes(32));
                    $expires = \CodeIgniter\I18n\Time::now()->addDays(30)->toDateTimeString();
                    $this->userModel->update($user->id, [
                        'remember_token'      => hash('sha256', $token),
                        'remember_expires_at' => $expires,
                    ]);
                    helper('cookie');
                    $response->setCookie([
                        'name'     => 'remember_me',
                        'value'    => $user->id . ':' . $token,
                        'expire'   => 30 * 24 * 60 * 60,
                        'httponly' => true,
                    ]);
                }

                return $response;
            }

            return view('templates/header', $data)
                 . view('auth/login', ['error' => lang('News.invalid_credentials'), 'locale' => $this->locale])
                 . view('templates/footer');
        }

        return view('templates/header', $data)
             . view('auth/login', ['locale' => $this->locale])
             . view('templates/footer');
    }

    public function register(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/' . $this->locale . '/');
        }

        $data = [
            'locale' => $this->locale,
            'title'  => lang('News.register'),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username' => 'required|alpha_numeric|min_length[3]|max_length[50]|is_unique[users.username]',
                'email'    => 'required|valid_email|max_length[100]|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'full_name'=> 'permit_empty|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return view('templates/header', $data)
                     . view('auth/register', ['validation' => $this->validator, 'locale' => $this->locale])
                     . view('templates/footer');
            }

            $this->userModel->insert([
                'username' => $this->request->getPost('username'),
                'email'    => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'full_name'=> $this->request->getPost('full_name'),
                'role'     => 'reader',
            ]);

            return redirect()->to('/' . $this->locale . '/login')
                           ->with('message', lang('News.register_success'));
        }

        return view('templates/header', $data)
             . view('auth/register', ['locale' => $this->locale])
             . view('templates/footer');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        $userId = session()->get('user_id');
        if ($userId) {
            $this->userModel->update($userId, [
                'remember_token'      => null,
                'remember_expires_at' => null,
            ]);
        }

        session()->destroy();

        $response = redirect()->to('/' . $this->locale . '/');
        $response->deleteCookie('remember_me');
        return $response;
    }

    public function forgotPassword(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/' . $this->locale . '/');
        }

        $data = [
            'locale' => $this->locale,
            'title'  => lang('News.forgot_password_title'),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email',
            ];

            if (!$this->validate($rules)) {
                return view('templates/header', $data)
                     . view('auth/forgot_password', ['validation' => $this->validator, 'locale' => $this->locale])
                     . view('templates/footer');
            }

            $email = $this->request->getPost('email');
            $user = $this->userModel->where('email', $email)->where('status', 1)->first();

            if (!$user) {
                return view('templates/header', $data)
                     . view('auth/forgot_password', ['error' => lang('News.email_not_found'), 'locale' => $this->locale])
                     . view('templates/footer');
            }

            // Generate token (secure random hex)
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);
            // Expire in 1 hour
            $expires = \CodeIgniter\I18n\Time::now()->addHours(1)->toDateTimeString();

            $this->userModel->update($user->id, [
                'reset_token'      => $hashedToken,
                'reset_expires_at' => $expires,
            ]);

            log_message('info', 'Password Reset Link for ' . $email . ': ' . site_url($this->locale . '/reset-password/' . $token));

            return view('templates/header', $data)
                 . view('auth/forgot_password', ['message' => lang('News.reset_link_sent'), 'locale' => $this->locale])
                 . view('templates/footer');
        }

        return view('templates/header', $data)
             . view('auth/forgot_password', ['locale' => $this->locale, 'error' => null, 'message' => null])
             . view('templates/footer');
    }

    public function resetPassword(string $token): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/' . $this->locale . '/');
        }

        $hashedToken = hash('sha256', $token);
        $user = $this->userModel->where('reset_token', $hashedToken)
                                ->where('status', 1)
                                ->first();

        // Check if user exists and token has not expired
        if ($user) {
            $expires = \CodeIgniter\I18n\Time::parse($user->reset_expires_at);
            $isExpired = $expires->isBefore(\CodeIgniter\I18n\Time::now());
        } else {
            $isExpired = true;
        }

        if (!$user || $isExpired) {
            $data = [
                'locale' => $this->locale,
                'title'  => lang('News.reset_password_title'),
            ];
            return view('templates/header', $data)
                 . view('auth/reset_password', ['error' => lang('News.invalid_or_expired_token'), 'locale' => $this->locale])
                 . view('templates/footer');
        }

        $data = [
            'locale' => $this->locale,
            'title'  => lang('News.reset_password_title'),
            'token'  => $token,
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
            ];

            if (!$this->validate($rules)) {
                return view('templates/header', $data)
                     . view('auth/reset_password', [
                         'validation' => $this->validator, 
                         'locale'     => $this->locale, 
                         'token'      => $token
                     ])
                     . view('templates/footer');
            }

            // Update user password and clear token
            $this->userModel->update($user->id, [
                'password'         => $this->request->getPost('password'),
                'reset_token'      => null,
                'reset_expires_at' => null,
            ]);

            return redirect()->to('/' . $this->locale . '/login')
                             ->with('message', lang('News.password_reset_success'));
        }

        return view('templates/header', $data)
             . view('auth/reset_password', ['locale' => $this->locale, 'token' => $token, 'error' => null])
             . view('templates/footer');
    }
}
