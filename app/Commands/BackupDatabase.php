<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\DatabaseBackup;

class BackupDatabase extends BaseCommand
{
    protected $group       = 'Backup';
    protected $name        = 'backup:database';
    protected $description = 'Create or manage database backups.';
    protected $usage       = 'backup:database [create|cleanup]';
    protected $arguments   = [
        'action' => 'Action: create or cleanup',
    ];

    public function run(array $params)
    {
        $action = $params[0] ?? 'create';
        $backup = new DatabaseBackup();

        if ($action === 'cleanup') {
            $deleted = $backup->cleanup(30);
            CLI::write("Cleanup complete. Deleted {$deleted} old backup(s).", 'green');
            return;
        }

        try {
            $filepath = $backup->create();
            $size = filesize($filepath);
            $sizeFormatted = $this->formatBytes($size);

            CLI::write('Database backup created successfully!', 'green');
            CLI::write("File: {$filepath}", 'white');
            CLI::write("Size: {$sizeFormatted}", 'white');
        } catch (\Exception $e) {
            CLI::error('Backup failed: ' . $e->getMessage());
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
