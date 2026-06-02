<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailVerificationToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'verification_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'email_verified_at'  => ['type' => 'DATETIME', 'null' => true],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['verification_token', 'email_verified_at']);
    }
}
