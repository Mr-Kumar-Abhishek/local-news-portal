<?php

namespace App\Libraries;

use Config\Database;

class DatabaseBackup
{
    protected $db;
    protected $backupPath;

    public function __construct(array $config = [])
    {
        $this->db = Database::connect();
        $this->backupPath = $config['backupPath'] ?? WRITEPATH . 'backups/';

        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    /**
     * Create a database backup.
     *
     * @return string The path to the backup file
     * @throws \RuntimeException
     */
    public function create(): string
    {
        $dbConfig = config('Database');
        $dbDriver = $dbConfig->default['DBDriver'];
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "backup_{$timestamp}";

        if ($dbDriver === 'SQLite3') {
            $dbPath = $dbConfig->default['database'];
            $backupFile = $this->backupPath . $filename . '.db';

            if (!copy($dbPath, $backupFile)) {
                throw new \RuntimeException('Failed to copy SQLite database file.');
            }

            return $backupFile;
        }

        // MySQL: manual table export
        $backupFile = $this->backupPath . $filename . '.sql';
        $handle = fopen($backupFile, 'w');

        if (!$handle) {
            throw new \RuntimeException('Failed to create backup file.');
        }

        fwrite($handle, "-- Hind Bihar Database Backup\n");
        fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Driver: {$dbDriver}\n\n");

        $tables = $this->db->listTables();

        foreach ($tables as $table) {
            // Drop and create table
            $createTable = $this->db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            if ($createTable) {
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $createTable['Create Table'] . ";\n\n");
            }

            // Export data
            $rows = $this->db->table($table)->get()->getResultArray();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $values = array_map(function ($val) {
                        if ($val === null) {
                            return 'NULL';
                        }
                        return "'" . str_replace("'", "''", $val) . "'";
                    }, $row);

                    fwrite($handle, "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n");
                }
                fwrite($handle, "\n");
            }
        }

        fclose($handle);

        return $backupFile;
    }

    /**
     * Delete backups older than specified days.
     *
     * @param int $keepDays Number of days to keep
     * @return int Number of files deleted
     */
    public function cleanup(int $keepDays = 30): int
    {
        $deleted = 0;
        $cutoff = time() - ($keepDays * 86400);

        $files = glob($this->backupPath . 'backup_*');
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    /**
     * List all backup files with size and date.
     *
     * @return array Array of backup info arrays
     */
    public function listBackups(): array
    {
        $backups = [];
        $files = glob($this->backupPath . 'backup_*');

        if ($files === false) {
            return [];
        }

        rsort($files); // newest first

        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path'     => $file,
                'size'     => filesize($file),
                'date'     => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        return $backups;
    }
}
