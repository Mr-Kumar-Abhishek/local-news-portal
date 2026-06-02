<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddArticleFlags extends Migration
{
    public function up()
    {
        $fields = [
            'is_featured'    => ['type' => 'INTEGER', 'default' => 0],
            'is_breaking'    => ['type' => 'INTEGER', 'default' => 0],
            'allow_comments' => ['type' => 'INTEGER', 'default' => 1],
        ];
        $this->forge->addColumn('articles', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('articles', ['is_featured', 'is_breaking', 'allow_comments']);
    }
}
