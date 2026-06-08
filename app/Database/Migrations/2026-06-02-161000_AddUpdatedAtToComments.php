<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToComments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('comments', [
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down()
    {
        // SQLite doesn't support DROP COLUMN
        if ($this->db->DBDriver !== 'SQLite3') {
            $this->forge->dropColumn('comments', 'updated_at');
        }
    }
}
