<?php

declare(strict_types=1);

use Kodhe\Framework\Database\Loader;

/**
 * Migration: add social-login columns to the users table.
 *
 * Mendukung integrasi kodhe/auth <-> kodhe/socialite (SocialiteAuth):
 *   - social_accounts : JSON map provider => data identitas sosial yang
 *                       ditautkan, mis.
 *                       {"google":{"id":"123","email":"a@b.c","name":"A",
 *                        "avatar":"...","linked_at":1758988800}}
 *                       Dipakai untuk lookup "sudah pernah login sosial?",
 *                       panel akun terhubung di dashboard, dan guard
 *                       disconnect (minimal satu kanal login tersisa).
 *   - social_tokens   : JSON map provider => token vault, mis.
 *                       {"google":{"access_token":"...","refresh_token":"...",
 *                        "expires_at":1758992400,"scope":"openid email"}}
 *                       Isi kolom ini terkelola otomatis oleh
 *                       SocialiteAuth::storeTokens()/accessTokenFor()
 *                       ketika config 'store_tokens' aktif — termasuk
 *                       refresh transparan & pembersihan saat disconnect.
 *
 * Nama kolom konsisten dengan default config
 * (Kodhe\Framework\Auth\SocialiteAuth: 'social_accounts_column' =>
 * 'social_accounts', konstanta TOKENS_COLUMN = 'social_tokens').
 * Idempotent: memeriksa schema sebelum menambah kolom sehingga aman
 * dijalankan ulang.
 */
return new class {

    /** @var array<string, string> column => DDL type */
    private array $columns = [
        'social_accounts' => 'TEXT NULL DEFAULT NULL',
        'social_tokens'   => 'TEXT NULL DEFAULT NULL',
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
     * Portable column check (SQLite pragma / MySQL information_schema),
     * mengikuti pola migration 2026_09_26_000003.
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
