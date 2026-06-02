<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class Users extends BaseController
{
    public function index(): string
    {
        $userModel = new UserModel();

        $data = [
            'locale'    => $this->locale,
            'title'     => 'User Management',
            'users'     => $userModel->getUsersWithCounts(),
            'user_name' => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/users/index', $data)
             . view('admin/templates/footer');
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/' . $this->locale . '/admin/users')
                           ->with('error', 'User not found');
        }

        $data = [
            'locale'    => $this->locale,
            'title'     => 'Edit User',
            'user'      => $user,
            'user_name' => $this->getCurrentUserName(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $updateData = [
                'full_name'          => $this->request->getPost('full_name'),
                'role'               => $this->request->getPost('role'),
                'status'             => $this->request->getPost('status') ?? 1,
                'language_preference' => $this->request->getPost('language_preference') ?? 'en',
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            $userModel->update($id, $updateData);

            return redirect()->to('/' . $this->locale . '/admin/users')
                           ->with('message', 'User updated successfully');
        }

        return view('admin/templates/header', $data)
             . view('admin/users/edit', $data)
             . view('admin/templates/footer');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $userModel = new UserModel();
        $userModel->delete($id);

        return redirect()->to('/' . $this->locale . '/admin/users')
                       ->with('message', 'User deleted successfully');
    }
}
