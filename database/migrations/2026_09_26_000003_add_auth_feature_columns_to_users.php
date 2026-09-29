<?php

declare(strict_types=1);

use Kodhe\Framework\Database\Loader;

/**
 * Migration: add auth-feature columns to the users table.
 *
 * Mendukung fitur-fitur baru paket kodhe/auth pada aplikasi contoh:
 *   - email_verified_at      : verifikasi email (non-null => terverifikasi)
 *   - verification_hash/_expires : token sekali-pakai (disimpan sebagai SHA-256)
 *   - password_reset_hash/_expires : token reset password (SHA-256 + expiry unix)
 *
 * Nama kolom konsisten dengan default config guard
 * (Kodhe\Framework\Auth\Auth::defaultConfig()). Idempotent: memeriksa
 * informasi schema sebelum menambah kolom sehingga aman dijalankan ulang.
 */
return new class {

    /** @var array<string, string> column => DDL type */
    private array $columns = [
        'email_verified_at'      => 'DATETIME NULL DEFAULT NULL',
        'verification_hash'      => "VARCHAR(64) NULL DEFAULT NULL",
        'verification_expires'   => 'INT NULL DEFAULT NULL',
        'password_reset_hash'    => 'VARCHAR(64) NULL DEFAULT NULL',
        'password_reset_expires' => 'INT NULL DEFAULT NULL',
    ];

    public function up(): void
    {
        $db = kodhe()->db;

        foreach ($this->columns as $name => $ddl) {
            if ($this->columnExists($db, $name)) {
                continue; // idempotent
            }
            $db->query('ALTER TABLE users ADD COLUMN ' . $name . ' ' . $ddl);
        }
    }

    public function down(): void
    {
        $db = kodhe()->db;

        foreach (array_reverse(array_keys($this->columns)) as $name) {
            if (!$this->columnExists($db, $name)) {
                continue;
            }
            $db->query('ALTER TABLE users DROP COLUMN ' . $name);
        }
    }

    /**
     * Portable column check (works on SQLite and MySQL via CI's
     * information_schema / pragma helpers where available).
     */
    private function columnExists(object $db, string $column): bool
    {
        if (!preg_match('/^[a-z_]+$/', $column)) {
            return false;
        }

        try {
            $driver = $db->dbdriver ?? '';
            if (str_contains((string) $driver, 'sqlite')) {
                $rows = $db->query('PRAGMA table_info(users)')->result_array();
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
                [$db->escape_str((string) $dbName), $db->escape_str('users'), $db->escape_str($column)]
            );
            $row = $q->row_array();
            return (int) ($row['c'] ?? 0) > 0;
        } catch (\Throwable) {
            return false;
        }
    }
};
