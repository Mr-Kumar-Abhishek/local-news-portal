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

                return redirect()->to('/' . $this->locale . '/admin/dashboard');
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

            return redirect()->to('/' . $this->locale . '/auth/login')
                           ->with('message', lang('News.register_success'));
        }

        return view('templates/header', $data)
             . view('auth/register', ['locale' => $this->locale])
             . view('templates/footer');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/' . $this->locale . '/');
    }
}
