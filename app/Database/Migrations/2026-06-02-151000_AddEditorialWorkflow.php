<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditorialWorkflow extends Migration
{
    public function up()
    {
        // Add editor_id column
        $this->forge->addColumn('articles', [
            'editor_id' => ['type' => 'INTEGER', 'null' => true],
        ]);

        // Drop old status trigger and create new one with expanded statuses
        $this->db->query('DROP TRIGGER IF EXISTS ' . $this->db->prefixTable('articles') . '_status_check');

        $this->db->query("CREATE TRIGGER " . $this->db->prefixTable('articles') . "_status_check BEFORE INSERT ON " . $this->db->prefixTable('articles') . " FOR EACH ROW
            WHEN NEW.status NOT IN ('draft', 'pending', 'approved', 'published', 'archived')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid status value. Must be draft, pending, approved, published, or archived.');
            END;");

        // Also create UPDATE trigger for status changes
        $this->db->query('DROP TRIGGER IF EXISTS ' . $this->db->prefixTable('articles') . '_status_update_check');

        $this->db->query("CREATE TRIGGER " . $this->db->prefixTable('articles') . "_status_update_check BEFORE UPDATE ON " . $this->db->prefixTable('articles') . " FOR EACH ROW
            WHEN NEW.status NOT IN ('draft', 'pending', 'approved', 'published', 'archived')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid status value. Must be draft, pending, approved, published, or archived.');
            END;");
    }

    public function down()
    {
        // Drop the update trigger
        $this->db->query('DROP TRIGGER IF EXISTS ' . $this->db->prefixTable('articles') . '_status_update_check');

        // Drop the insert trigger
        $this->db->query('DROP TRIGGER IF EXISTS ' . $this->db->prefixTable('articles') . '_status_check');

        // Recreate original insert trigger
        $this->db->query("CREATE TRIGGER " . $this->db->prefixTable('articles') . "_status_check BEFORE INSERT ON " . $this->db->prefixTable('articles') . " FOR EACH ROW
            WHEN NEW.status NOT IN ('draft', 'published', 'archived')
            BEGIN
                SELECT RAISE(ABORT, 'Invalid status value.');
            END;");

        // Remove editor_id
        $this->forge->dropColumn('articles', 'editor_id');
    }
}
