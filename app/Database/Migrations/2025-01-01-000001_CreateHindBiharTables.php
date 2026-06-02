<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHindBiharTables extends Migration
{
    public function up()
    {
        // Users table
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'username'          => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'password'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'full_name'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'role'              => ['type' => 'ENUM', 'constraint' => ['admin', 'editor', 'journalist', 'reader'], 'default' => 'reader'],
            'language_preference' => ['type' => 'ENUM', 'constraint' => ['hi', 'en'], 'default' => 'en'],
            'status'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // Categories table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'name_en'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'name_hi'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'parent_id'   => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => null],
            'status'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('categories', true);

        // Tags table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'name_en'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'name_hi'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tags', true);

        // Articles table
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'title_en'       => ['type' => 'VARCHAR', 'constraint' => 500],
            'title_hi'       => ['type' => 'VARCHAR', 'constraint' => 500],
            'content_en'     => ['type' => 'LONGTEXT', 'null' => true],
            'content_hi'     => ['type' => 'LONGTEXT', 'null' => true],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 500, 'unique' => true],
            'excerpt_en'     => ['type' => 'TEXT', 'null' => true],
            'excerpt_hi'     => ['type' => 'TEXT', 'null' => true],
            'featured_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'category_id'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'author_id'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'language'       => ['type' => 'ENUM', 'constraint' => ['hi', 'en', 'both'], 'default' => 'both'],
            'news_section'   => ['type' => 'ENUM', 'constraint' => ['international', 'national', 'local'], 'default' => 'local'],
            'status'         => ['type' => 'ENUM', 'constraint' => ['draft', 'published', 'archived'], 'default' => 'draft'],
            'published_at'   => ['type' => 'DATETIME', 'null' => true],
            'featured'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'view_count'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('author_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('articles', true);

        // Article-Tags pivot table
        $this->forge->addField([
            'article_id' => ['type' => 'INT', 'constraint' => 11],
            'tag_id'     => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addKey(['article_id', 'tag_id'], true);
        $this->forge->addForeignKey('article_id', 'articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('article_tags', true);

        // Comments table
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'article_id'   => ['type' => 'INT', 'constraint' => 11],
            'user_id'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'author_name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'author_email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'body'         => ['type' => 'TEXT'],
            'status'       => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected'], 'default' => 'pending'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('article_id', 'articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('comments', true);

        // Media table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'filename'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'filepath'    => ['type' => 'VARCHAR', 'constraint' => 500],
            'filetype'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'filesize'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'alt_text_en' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'alt_text_hi' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'uploaded_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('media', true);

        // Settings table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'key'        => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('settings', true);
        $this->forge->dropTable('media', true);
        $this->forge->dropTable('comments', true);
        $this->forge->dropTable('article_tags', true);
        $this->forge->dropTable('articles', true);
        $this->forge->dropTable('tags', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('users', true);
    }
}
