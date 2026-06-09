<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReportedToComments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('comments', [
            'is_reported' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'status'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('comments', 'is_reported');
    }
}
