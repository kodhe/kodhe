<?php

declare(strict_types=1);

/**
 * Migration: seed default roles, permissions and ACL rules.
 *
 * Memberi aplikasi contoh sebuah skema otorisasi yang langsung bisa dicoba:
 *   - superadmin (level 0)  : '*' (semua izin)
 *   - admin      (level 10) : mengelola konten & pengguna, inherit editor
 *   - editor     (level 20) : membuat/mengubah/menghapus artikel, inherit author
 *   - author     (level 30) : membuat artikel + mengubah milik sendiri
 *   - viewer     (level 40) : hanya membaca
 *
 * ACL: satu deny-except-own untuk post.edit agar fitur "scope own" ikut
 * terdemo, dan role senior otomatis menyerap izin junior lewat 'inherits'.
 *
 * Insert idempotent (dicek per nama), aman dijalankan ulang.
 */
return new class {

    /** @var array<int,array> name, title, level, inherits */
    private array $roles = [
        ['superadmin', 'Super Administrator', 0,  ''],
        ['admin',      'Administrator',       10, 'editor'],
        ['editor',     'Editor',              20, 'author'],
        ['author',     'Author',              30, 'viewer'],
        ['viewer',     'Viewer',              40, ''],
    ];

    /** @var array<string,string[]> role => permission names */
    private array $permissions = [
        'superadmin' => ['*'],
        'admin'      => ['user.view', 'user.manage', 'post.view', 'post.create', 'post.edit', 'post.delete'],
        'editor'     => ['post.view', 'post.create', 'post.edit', 'post.delete'],
        'author'     => ['post.view', 'post.create', 'post.edit'],
        'viewer'     => ['post.view'],
    ];

    /** @var array<int,array> target, subject, effect, priority, scope(JSON), enabled, expires */
    private array $acl = [
        // Editor ke bawah hanya boleh MENGUBAH artikel miliknya sendiri.
        // Prioritas tinggi menang atas grant biasa dari hierarchy.
        ['post.edit', 'role:editor', 'deny', 50, '{"any":true}', 1, null],
        ['post.edit', 'role:editor', 'allow', 60, '{"own":true}', 1, null],
        ['post.edit', 'role:author', 'deny', 50, '{"any":true}', 1, null],
        ['post.edit', 'role:author', 'allow', 60, '{"own":true}', 1, null],
    ];

    public function up(): void
    {
        $db = kodhe()->db;
        $now = date('Y-m-d H:i:s');

        foreach ($this->roles as [$name, $title, $level, $inherits]) {
            $row = $db->query(
                'SELECT id FROM roles WHERE name = ?',
                [$db->escape_str($name)]
            )->row();
            if ($row !== null && $row !== false) {
                continue;
            }
            $db->query(
                'INSERT INTO roles (name, title, level, inherits, created_at) VALUES (?, ?, ?, ?, ?)',
                [
                    $db->escape_str($name),
                    $db->escape_str($title),
                    (int) $level,
                    $db->escape_str($inherits),
                    $now,
                ]
            );
        }

        foreach ($this->permissions as $roleName => $perms) {
            foreach ($perms as $perm) {
                $row = $db->query(
                    'SELECT id FROM permissions WHERE role_name = ? AND name = ?',
                    [$db->escape_str($roleName), $db->escape_str($perm)]
                )->row();
                if ($row !== null && $row !== false) {
                    continue;
                }
                $db->query(
                    'INSERT INTO permissions (role_name, name) VALUES (?, ?)',
                    [$db->escape_str($roleName), $db->escape_str($perm)]
                );
            }
        }

        foreach ($this->acl as [$target, $subject, $effect, $priority, $scope, $enabled, $expires]) {
            $row = $db->query(
                'SELECT id FROM acl WHERE target = ? AND subject = ? AND effect = ? AND scope = ?',
                [$db->escape_str($target), $db->escape_str($subject), $db->escape_str($effect), $db->escape_str($scope)]
            )->row();
            if ($row !== null && $row !== false) {
                continue;
            }
            $db->query(
                'INSERT INTO acl (target, subject, effect, priority, scope, enabled, expires) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $db->escape_str($target),
                    $db->escape_str($subject),
                    $db->escape_str($effect),
                    (int) $priority,
                    $db->escape_str($scope),
                    (int) $enabled,
                    $expires === null ? null : (int) $expires,
                ]
            );
        }

        // Assign the seeded users to roles (admin@example.com => admin).
        $this->assignRole($db, 'admin@example.com', 'admin');
        $this->assignRole($db, 'demo@kodhe.test', 'viewer');
    }

    public function down(): void
    {
        $db = kodhe()->db;

        foreach ($this->roles as [$name]) {
            $db->query('DELETE FROM roles WHERE name = ?', [$db->escape_str($name)]);
            $db->query('DELETE FROM permissions WHERE role_name = ?', [$db->escape_str($name)]);
        }
        $db->query("DELETE FROM acl WHERE subject IN ('role:editor','role:author')");
    }

    private function assignRole(object $db, string $email, string $roleName): void
    {
        $role = $db->query(
            'SELECT id FROM roles WHERE name = ?',
            [$db->escape_str($roleName)]
        )->row();
        if ($role === null || $role === false) {
            return;
        }
        $db->query(
            'UPDATE users SET role_id = ? WHERE email = ? AND role_id IS NULL',
            [(int) $role->id, $db->escape_str($email)]
        );
    }
};
