<?php

namespace App\Controllers\Admin;

use App\Models\CommentModel;

class Comments extends BaseController
{
    public function index(): string
    {
        $commentModel = new CommentModel();

        $status = $this->request->getGet('status');

        if ($status === 'reported') {
            $comments = $commentModel->getReportedComments();
        } else {
            $comments = $commentModel->getAllWithDetails();
            if (!empty($status)) {
                $comments = array_filter($comments, fn($c) => $c['status'] === $status);
            }
        }

        $pendingCount = $commentModel->getPendingCount();

        $data = [
            'locale'        => $this->locale,
            'title'         => 'Comment Moderation',
            'comments'      => $comments,
            'status'        => $status,
            'pending_count' => $pendingCount,
            'user_name'     => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/comments/index', $data)
             . view('admin/templates/footer');
    }

    public function approve(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $commentModel = new CommentModel();
        $comment = $commentModel->find($id);
        $commentModel->update($id, ['status' => 'approved']);

        // Log activity
        if ($comment) {
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'comment_approved',
                'entity_type' => 'comment',
                'entity_id' => $id,
                'description' => 'Comment #' . $id . ' approved',
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/comments')
                       ->with('message', 'Comment approved');
    }

    public function reject(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $commentModel = new CommentModel();
        $comment = $commentModel->find($id);
        $commentModel->update($id, ['status' => 'rejected']);

        // Log activity
        if ($comment) {
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'comment_rejected',
                'entity_type' => 'comment',
                'entity_id' => $id,
                'description' => 'Comment #' . $id . ' rejected',
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/comments')
                       ->with('message', 'Comment rejected');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $commentModel = new CommentModel();
        $comment = $commentModel->find($id);
        $commentModel->delete($id);

        // Log activity
        if ($comment) {
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'comment_deleted',
                'entity_type' => 'comment',
                'entity_id' => $id,
                'description' => 'Comment #' . $id . ' deleted',
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/comments')
                       ->with('message', 'Comment deleted');
    }
}
