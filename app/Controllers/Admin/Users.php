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

    public function create(): string
    {
        $data = [
            'locale'    => $this->locale,
            'title'     => 'Create User',
            'user_name' => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/users/create', $data)
             . view('admin/templates/footer');
    }

    public function save(): \CodeIgniter\HTTP\RedirectResponse
    {
        $userModel = new UserModel();

        $rules = [
            'username'     => 'required|alpha_numeric|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'        => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'password'     => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]',
            'full_name'    => 'permit_empty|max_length[100]',
            'role'         => 'required|in_list[user,editor,admin]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = $userModel->insert([
            'username'  => $this->request->getPost('username'),
            'email'     => $this->request->getPost('email'),
            'password'  => $this->request->getPost('password'),
            'full_name' => $this->request->getPost('full_name'),
            'role'      => $this->request->getPost('role'),
            'status'    => $this->request->getPost('status') ?? 'active',
        ]);

        // Log activity
        $activityLog = new \App\Models\ActivityLogModel();
        $activityLog->log([
            'user_id' => $this->getCurrentUserId(),
            'action' => 'user_created',
            'entity_type' => 'user',
            'entity_id' => $userId,
            'description' => "User '{$this->request->getPost('username')}' created",
            'ip_address' => $this->request->getIPAddress(),
        ]);

        return redirect()->to('/' . $this->locale . '/admin/users')
                       ->with('message', 'User created successfully');
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

            // Log activity
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'user_updated',
                'entity_type' => 'user',
                'entity_id' => $id,
                'description' => "User '{$user->username}' updated",
                'ip_address' => $this->request->getIPAddress(),
            ]);

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
        $user = $userModel->find($id);
        $userModel->delete($id);

        // Log activity
        if ($user) {
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'user_deleted',
                'entity_type' => 'user',
                'entity_id' => $id,
                'description' => "User '{$user->username}' deleted",
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/users')
                       ->with('message', 'User deleted successfully');
    }
}
