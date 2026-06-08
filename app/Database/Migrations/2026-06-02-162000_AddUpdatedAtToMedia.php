<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToMedia extends Migration
{
    public function up()
    {
        $this->forge->addColumn('media', [
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down()
    {
        if ($this->db->DBDriver !== 'SQLite3') {
            $this->forge->dropColumn('media', 'updated_at');
        }
    }
}
