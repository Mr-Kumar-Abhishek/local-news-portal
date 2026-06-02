<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHindBiharTables extends Migration
{
    public function up()
    {
        // Enable foreign keys for SQLite
        $this->db->query('PRAGMA foreign_keys = ON;');

        // Users table
        $this->forge->addField([
            'id'                  => ['type' => 'INTEGER', 'auto_increment' => true],
            'username'            => ['type' => 'VARCHAR', 'constraint' => 50],
            'email'               => ['type' => 'VARCHAR', 'constraint' => 100],
            'password'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'full_name'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'role'                => ['type' => 'TEXT', 'default' => 'reader'],
            'language_preference' => ['type' => 'TEXT', 'default' => 'en'],
            'status'              => ['type' => 'INTEGER', 'default' => 1],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users', true);

        // Add CHECK constraints for ENUM-like behaviour on users table
        $this->db->query("CREATE TRIGGER IF NOT EXISTS users_role_check BEFORE INSERT ON " . $this->db->prefixTable('users') . " FOR EACH ROW
            WHEN NEW.role NOT IN ('admin', 'editor', 'journalist', 'reader')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid role value. Must be admin, editor, journalist, or reader.');
            END;");
        $this->db->query("CREATE TRIGGER IF NOT EXISTS users_language_check BEFORE INSERT ON " . $this->db->prefixTable('users') . " FOR EACH ROW
            WHEN NEW.language_preference NOT IN ('hi', 'en')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid language value. Must be hi or en.');
            END;");

        // Categories table
        $this->forge->addField([
            'id'          => ['type' => 'INTEGER', 'auto_increment' => true],
            'name_en'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'name_hi'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
            'parent_id'   => ['type' => 'INTEGER', 'null' => true, 'default' => null],
            'status'      => ['type' => 'INTEGER', 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('parent_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('categories', true);

        // Tags table
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
            'name_en'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'name_hi'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('tags', true);

        // Articles table
        $this->forge->addField([
            'id'             => ['type' => 'INTEGER', 'auto_increment' => true],
            'title_en'       => ['type' => 'VARCHAR', 'constraint' => 500],
            'title_hi'       => ['type' => 'VARCHAR', 'constraint' => 500],
            'content_en'     => ['type' => 'TEXT', 'null' => true],
            'content_hi'     => ['type' => 'TEXT', 'null' => true],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 500],
            'excerpt_en'     => ['type' => 'TEXT', 'null' => true],
            'excerpt_hi'     => ['type' => 'TEXT', 'null' => true],
            'featured_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'category_id'    => ['type' => 'INTEGER', 'null' => true],
            'author_id'      => ['type' => 'INTEGER', 'null' => true],
            'language'       => ['type' => 'TEXT', 'default' => 'both'],
            'news_section'   => ['type' => 'TEXT', 'default' => 'local'],
            'status'         => ['type' => 'TEXT', 'default' => 'draft'],
            'published_at'   => ['type' => 'DATETIME', 'null' => true],
            'featured'       => ['type' => 'INTEGER', 'default' => 0],
            'view_count'     => ['type' => 'INTEGER', 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('author_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('articles', true);

        // Add CHECK constraints for ENUM-like behaviour on articles table
        $this->db->query("CREATE TRIGGER IF NOT EXISTS articles_language_check BEFORE INSERT ON " . $this->db->prefixTable('articles') . " FOR EACH ROW
            WHEN NEW.language NOT IN ('hi', 'en', 'both')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid language value. Must be hi, en, or both.');
            END;");
        $this->db->query("CREATE TRIGGER IF NOT EXISTS articles_section_check BEFORE INSERT ON " . $this->db->prefixTable('articles') . " FOR EACH ROW
            WHEN NEW.news_section NOT IN ('international', 'national', 'local')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid news_section value.');
            END;");
        $this->db->query("CREATE TRIGGER IF NOT EXISTS articles_status_check BEFORE INSERT ON " . $this->db->prefixTable('articles') . " FOR EACH ROW
            WHEN NEW.status NOT IN ('draft', 'published', 'archived')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid status value.');
            END;");

        // Article-Tags pivot table
        $this->forge->addField([
            'article_id' => ['type' => 'INTEGER'],
            'tag_id'     => ['type' => 'INTEGER'],
        ]);
        $this->forge->addKey(['article_id', 'tag_id'], true);
        $this->forge->addForeignKey('article_id', 'articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('article_tags', true);

        // Comments table
        $this->forge->addField([
            'id'           => ['type' => 'INTEGER', 'auto_increment' => true],
            'article_id'   => ['type' => 'INTEGER'],
            'user_id'      => ['type' => 'INTEGER', 'null' => true],
            'author_name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'author_email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'body'         => ['type' => 'TEXT'],
            'status'       => ['type' => 'TEXT', 'default' => 'pending'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('article_id', 'articles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('comments', true);

        // Add CHECK constraint for comment status
        $this->db->query("CREATE TRIGGER IF NOT EXISTS comments_status_check BEFORE INSERT ON " . $this->db->prefixTable('comments') . " FOR EACH ROW
            WHEN NEW.status NOT IN ('pending', 'approved', 'rejected')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid comment status value.');
            END;");

        // Media table
        $this->forge->addField([
            'id'          => ['type' => 'INTEGER', 'auto_increment' => true],
            'filename'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'filepath'    => ['type' => 'VARCHAR', 'constraint' => 500],
            'filetype'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'filesize'    => ['type' => 'INTEGER', 'null' => true],
            'alt_text_en' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'alt_text_hi' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'uploaded_by' => ['type' => 'INTEGER', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('media', true);

        // Settings table
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
            'key'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('key');
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
