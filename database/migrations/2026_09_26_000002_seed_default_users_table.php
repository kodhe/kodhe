<?php

declare(strict_types=1);

/**
 * Migration: seed default users.
 *
 * Dijalankan setelah create_users_table. Menyisipkan user bawaan:
 *   - demo@kodhe.test / password: "password" (dev only — ganti segera!)
 *     (akun ini yang ditampilkan pada GUI login "Demo credentials")
 *   - admin@example.com / password: "password" (akun admin legacy)
 *
 * Insert bersifat idempotent (dicek per email), sehingga aman dijalankan
 * ulang dan tidak menimpa data yang sudah diubah. Hash dibuat dengan
 * password_hash() bcrypt cost 11 — sama seperti Auth::hashPassword().
 */
return new class {

    /** @var array Default accounts to seed. */
    private array $seed = [
        [
            'name'  => 'Demo User',
            'email' => 'demo@kodhe.test',
            // DEV ONLY. Ganti password ini di produksi!
            'pass'  => 'password',
        ],
        [
            'name'  => 'Admin',
            'email' => 'admin@example.com',
            // DEV ONLY. Ganti password ini di produksi!
            'pass'  => 'password',
        ],
    ];

    public function up(): void
    {
        $db = kodhe()->db;

        foreach ($this->seed as $user) {
            // Skip jika email sudah ada (idempotent, tidak duplikat saat re-run).
            $row = $db->query(
                'SELECT id FROM users WHERE email = ?',
                [$db->escape_str($user['email'])]
            )->row();

            if ($row !== null && $row !== false) {
                continue;
            }

            $now = date('Y-m-d H:i:s');

            $db->query(
                'INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',
                [
                    $db->escape_str($user['name']),
                    $db->escape_str($user['email']),
                    $db->escape_str(password_hash($user['pass'], PASSWORD_BCRYPT, ['cost' => 11])),
                    $now,
                    $now,
                ]
            );
        }
    }

    public function down(): void
    {
        $db = kodhe()->db;

        foreach ($this->seed as $user) {
            $db->query(
                'DELETE FROM users WHERE email = ?',
                [$db->escape_str($user['email'])]
            );
        }
    }
};
