<?php

namespace App\Controllers\Admin;

use App\Models\ActivityLogModel;

class ActivityLog extends BaseController
{
    public function index(): string
    {
        $activityLogModel = new ActivityLogModel();

        $data = [
            'locale'    => $this->locale,
            'title'     => 'Activity Log',
            'logs'      => $activityLogModel->getRecent(100),
            'user_name' => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/activity/index', $data)
             . view('admin/templates/footer');
    }
}
