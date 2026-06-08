<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentIdToComments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('comments', [
            'parent_id' => [
                'type'       => 'INTEGER',
                'null'       => true,
                'default'    => null,
            ],
        ]);

        // SQLite doesn't support ALTER TABLE ADD CONSTRAINT for foreign keys,
        // so we only add an index for the parent_id column.
        $this->db->query('CREATE INDEX IF NOT EXISTS comments_parent_id_index ON ' . $this->db->prefixTable('comments') . ' (parent_id)');
    }

    public function down()
    {
        // SQLite doesn't support DROP COLUMN, so this is a no-op for SQLite.
        // For MySQL/MariaDB, it would drop the column.
        if ($this->db->DBDriver !== 'SQLite3') {
            $this->forge->dropColumn('comments', 'parent_id');
        }
    }
}
