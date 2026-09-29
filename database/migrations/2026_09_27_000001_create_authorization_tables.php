<?php

declare(strict_types=1);

use Kodhe\Framework\Database\Loader;

/**
 * Migration: create the authorization tables consumed by kodhe/auth's
 * AuthorizableProviderInterface (roles, permissions, ACL rules).
 *
 * Shapes match the guard's expectations exactly:
 *   - roles.level          => RoleHierarchy ('level' + optional 'inherits')
 *   - permissions.role_name => permissions granted to a role
 *   - acl.*                => raw Acl rule rows (target/subject/effect/
 *                             priority/scope/enabled/expires)
 *
 * Idempotent: uses create_table(ifNotExists = TRUE), so re-running is safe.
 */
return new class {

    public function up(): void
    {
        $forge = Loader::dbforge(null, true);

        // ---- roles ----------------------------------------------------------
        $forge->add_field([
            'id'          => ['type' => 'INT', 'constraint' => 9, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 64],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => TRUE],
            'level'       => ['type' => 'INT', 'constraint' => 11, 'default' => 100], // 0 = superadmin
            'inherits'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE], // comma-separated child roles
            'created_at'  => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $forge->add_key('id', TRUE);
        $forge->add_key('name');
        $forge->create_table('roles', TRUE);

        // ---- users.role_id bridge ------------------------------------------
        $this->addColumn('users', 'role_id', 'INT NULL DEFAULT NULL');

        // ---- permissions ------------------------------------------------------
        $forge->add_field([
            'id'     => ['type' => 'INT', 'constraint' => 9, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'role_name' => ['type' => 'VARCHAR', 'constraint' => 64],
            'name'      => ['type' => 'VARCHAR', 'constraint' => 120],
        ]);
        $forge->add_key('id', TRUE);
        $forge->add_key('role_name');
        $forge->create_table('permissions', TRUE);

        // ---- acl rules ---------------------------------------------------------
        $forge->add_field([
            'id'        => ['type' => 'INT', 'constraint' => 9, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'target'    => ['type' => 'VARCHAR', 'constraint' => 120],           // permission pattern, e.g. "post.edit"
            'subject'   => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => TRUE], // '*', '%', '?', role, '42', 'group:x'
            'effect'    => ['type' => 'VARCHAR', 'constraint' => 8, 'default' => 'allow'],
            'priority'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'scope'     => ['type' => 'TEXT', 'null' => TRUE],                   // JSON object matched against context
            'enabled'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'expires'   => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE], // unix timestamp; NULL = never
        ]);
        $forge->add_key('id', TRUE);
        $forge->create_table('acl', TRUE);
    }

    public function down(): void
    {
        $forge = Loader::dbforge(null, true);
        $forge->drop_table('acl', TRUE);
        $forge->drop_table('permissions', TRUE);
        $forge->drop_table('roles', TRUE);
        $this->dropColumn('users', 'role_id');
    }

    /**
     * Portable ADD COLUMN (SQLite/MySQL) guarded by an existence check.
     */
    private function addColumn(string $table, string $column, string $ddl): void
    {
        if (!preg_match('/^[a-z_]+$/', $table) || !preg_match('/^[a-z_]+$/', $column)) {
            return;
        }
        $db = kodhe()->db;
        if ($this->columnExists($db, $table, $column)) {
            return; // idempotent
        }
        $db->query("ALTER TABLE {$table} ADD COLUMN {$column} " . $ddl);
    }

    private function dropColumn(string $table, string $column): void
    {
        if (!preg_match('/^[a-z_]+$/', $table) || !preg_match('/^[a-z_]+$/', $column)) {
            return;
        }
        $db = kodhe()->db;
        if (!$this->columnExists($db, $table, $column)) {
            return;
        }
        $db->query("ALTER TABLE {$table} DROP COLUMN {$column}");
    }

    private function columnExists(object $db, string $table, string $column): bool
    {
        try {
            $driver = $db->dbdriver ?? '';
            if (str_contains((string) $driver, 'sqlite')) {
                $rows = $db->query('PRAGMA table_info(' . $db->protect_identifier($table) . ')')->result_array();
                foreach ($rows as $row) {
                    if (($row['name'] ?? '') === $column) {
                        return true;
                    }
                }
                return false;
            }

            $dbName = $db->database ?? '';
            $q = $db->query(
                'SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                [$db->escape_str((string) $dbName), $db->escape_str($table), $db->escape_str($column)]
            );
            $row = $q->row_array();
            return (int) ($row['c'] ?? 0) > 0;
        } catch (\Throwable) {
            return false;
        }
    }
};
