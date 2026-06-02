<?php

namespace App\Controllers\Admin;

use App\Models\CommentModel;

class Comments extends BaseController
{
    public function index(): string
    {
        $commentModel = new CommentModel();

        $data = [
            'locale'    => $this->locale,
            'title'     => 'Comment Moderation',
            'comments'  => $commentModel->getAllWithDetails(),
            'user_name' => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/comments/index', $data)
             . view('admin/templates/footer');
    }

    public function approve(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $commentModel = new CommentModel();
        $commentModel->update($id, ['status' => 'approved']);

        return redirect()->to('/' . $this->locale . '/admin/comments')
                       ->with('message', 'Comment approved');
    }

    public function reject(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $commentModel = new CommentModel();
        $commentModel->update($id, ['status' => 'rejected']);

        return redirect()->to('/' . $this->locale . '/admin/comments')
                       ->with('message', 'Comment rejected');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $commentModel = new CommentModel();
        $commentModel->delete($id);

        return redirect()->to('/' . $this->locale . '/admin/comments')
                       ->with('message', 'Comment deleted');
    }
}
