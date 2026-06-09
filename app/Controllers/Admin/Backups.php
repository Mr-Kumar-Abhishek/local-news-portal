<?php

namespace App\Controllers\Admin;

use App\Libraries\DatabaseBackup;

class Backups extends BaseController
{
    public function index(): string
    {
        $backup = new DatabaseBackup();
        $backups = $backup->listBackups();

        $data = [
            'locale'    => $this->locale,
            'title'     => 'Database Backups',
            'backups'   => $backups,
            'user_name' => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/backups/index', $data)
             . view('admin/templates/footer');
    }

    public function create(): \CodeIgniter\HTTP\RedirectResponse
    {
        $backup = new DatabaseBackup();

        try {
            $backup->create();

            // Log activity
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'backup_created',
                'entity_type' => 'backup',
                'entity_id' => null,
                'description' => 'Manual database backup created',
                'ip_address' => $this->request->getIPAddress(),
            ]);

            return redirect()->to('/' . $this->locale . '/admin/backups')
                           ->with('message', 'Database backup created successfully');
        } catch (\Exception $e) {
            return redirect()->to('/' . $this->locale . '/admin/backups')
                           ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(string $filename): \CodeIgniter\HTTP\Response
    {
        $backup = new DatabaseBackup();
        $backups = $backup->listBackups();

        $found = null;
        foreach ($backups as $b) {
            if ($b['filename'] === $filename) {
                $found = $b;
                break;
            }
        }

        if (!$found || !file_exists($found['path'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Backup file not found.');
        }

        return $this->response->download($found['path'], null);
    }

    public function delete(string $filename): \CodeIgniter\HTTP\RedirectResponse
    {
        $backup = new DatabaseBackup();
        $backups = $backup->listBackups();

        $found = null;
        foreach ($backups as $b) {
            if ($b['filename'] === $filename) {
                $found = $b;
                break;
            }
        }

        if ($found && file_exists($found['path'])) {
            unlink($found['path']);

            // Log activity
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'backup_deleted',
                'entity_type' => 'backup',
                'entity_id' => null,
                'description' => "Backup '{$filename}' deleted",
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/backups')
                       ->with('message', 'Backup deleted successfully');
    }
}
