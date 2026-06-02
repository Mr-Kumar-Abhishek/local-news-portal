<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberAndResetToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'remember_token'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'remember_expires_at' => ['type' => 'DATETIME', 'null' => true],
            'reset_token'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'reset_expires_at'    => ['type' => 'DATETIME', 'null' => true],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['remember_token', 'remember_expires_at', 'reset_token', 'reset_expires_at']);
    }
}
