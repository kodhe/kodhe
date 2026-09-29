<?php

declare(strict_types=1);

namespace App\Providers;

use Kodhe\Framework\Auth\Contracts\AuthorizableProviderInterface;
use Kodhe\Framework\Auth\Contracts\RegisterableProviderInterface;
use Kodhe\Framework\Auth\Contracts\UpdatableProviderInterface;
use Kodhe\Framework\Auth\Contracts\UserProviderInterface;

/**
 * Bridges the kodhe/auth guard to the application's "users" table.
 *
 * The guard itself never touches the database; it delegates all lookups
 * to a class implementing UserProviderInterface. This is the example
 * implementation for the kodhe demo app (SQLite via CI query builder).
 *
 * Also implements the optional contracts so registration, password
 * change/reset, e-mail verification AND authorization (roles, permission
 * grants, role hierarchy, ACL rules — see roles/permissions/acl tables in
 * database/migrations) are available in the demo app.
 */
class DatabaseUserProvider implements UserProviderInterface, UpdatableProviderInterface, RegisterableProviderInterface, AuthorizableProviderInterface
{
    private string $table;

    public function __construct(?string $table = null)
    {
        $this->table = $table ?? 'users';
    }

    /** {@inheritdoc} */
    public function retrieveByIdentifier(string $identifier, string $value, array $extra = []): ?array
    {
        $db = $this->db();
        if ($db === null || !preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            return null;
        }

        $db->where($identifier, $value);
        foreach ($extra as $col => $val) {
            if (preg_match('/^[A-Za-z0-9_]+$/', (string) $col)) {
                $db->where($col, $val);
            }
        }

        $row = $db->get($this->table, 1)->row_array();
        return is_array($row) && $row !== [] ? $row : null;
    }

    /** {@inheritdoc} */
    public function retrieveById(int|string $id): ?array
    {
        $db = $this->db();
        if ($db === null) {
            return null;
        }

        $row = $db->where('id', $id)->get($this->table, 1)->row_array();
        return is_array($row) && $row !== [] ? $row : null;
    }

    /** {@inheritdoc} */
    public function updateRememberToken(int|string $id, ?string $token): void
    {
        $db = $this->db();
        if ($db === null) {
            return;
        }
        $db->where('id', $id)->update($this->table, ['remember_token' => $token]);
    }

    /**
     * Optional hook used by the guard for transparent password rehashing.
     */
    public function updatePassword(int|string $id, string $hash): void
    {
        $db = $this->db();
        if ($db === null) {
            return;
        }
        $db->where('id', $id)->update($this->table, ['password' => $hash]);
    }

    /** {@inheritdoc} */
    public function updateUser(int|string $id, array $columns): void
    {
        $db = $this->db();
        if ($db === null || $columns === []) {
            return;
        }
        // Defensive: only plain identifier columns reach the builder.
        $safe = [];
        foreach ($columns as $col => $val) {
            if (preg_match('/^[A-Za-z0-9_]+$/', (string) $col)) {
                $safe[$col] = $val;
            }
        }
        if ($safe !== []) {
            $db->where('id', $id)->update($this->table, $safe);
        }
    }

    /** {@inheritdoc} */
    public function hasIdentifier(string $identifier, string $value): bool
    {
        return $this->retrieveByIdentifier($identifier, $value) !== null;
    }

    /** {@inheritdoc} */
    public function createUser(array $attributes, string $passwordHash): int|string
    {
        $db = $this->db();
        if ($db === null) {
            throw new \RuntimeException('DatabaseUserProvider: no active DB connection.');
        }

        $now = date('Y-m-d H:i:s');
        $data = array_merge($attributes, [
            'password'   => $passwordHash,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $ok = $db->insert($this->table, $data);
        if ($ok === false) {
            throw new \RuntimeException('DatabaseUserProvider: user insert failed.');
        }

        $id = $db->insert_id();
        if ($id === false || $id === null || $id === '') {
            // Some drivers do not report insert_id — fall back to lookup.
            $row = $db->order_by('id', 'DESC')->get($this->table, 1)->row_array();
            $id = $row['id'] ?? 0;
        }

        return is_numeric($id) ? (int) $id : (string) $id;
    }

    // ------------------------------------------------------------------
    // AuthorizableProviderInterface — roles / permissions / ACL storage
    // (backed by the roles, permissions and acl tables created by
    // database/migrations/2026_09_27_000001_create_authorization_tables.php)
    // ------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Resolves users.role_id -> roles.name. Falls back to the legacy
     * "role" column (comma-separated names allowed) when the user has no
     * role_id bridge or the referenced role row is missing.
     */
    public function rolesForUser(int|string $userId): array
    {
        $db = $this->db();
        if ($db === null) {
            return [];
        }

        try {
            $user = $this->retrieveById($userId);
            if ($user === null) {
                return [];
            }

            // Primary path: users.role_id -> roles.name (ordered by level,
            // lower level = more power = primary role first).
            if (!empty($user['role_id'])) {
                $rows = $db->where('id', (int) $user['role_id'])
                    ->order_by('level', 'ASC')
                    ->get('roles')
                    ->result_array();
                $names = [];
                foreach ($rows as $row) {
                    if (isset($row['name']) && $row['name'] !== '') {
                        $names[] = (string) $row['name'];
                    }
                }
                if ($names !== []) {
                    return $names;
                }
            }

            // Fallback: legacy plain "role" column on the user record.
            $raw = (string) ($user['role'] ?? '');
            $names = [];
            foreach (array_filter(array_map('trim', explode(',', $raw))) as $n) {
                $names[] = $n;
            }
            return $names;
        } catch (\Throwable) {
            // Missing tables / broken connection must never break auth:
            // the guard then falls back to the configured role column.
            return [];
        }
    }

    /**
     * {@inheritdoc}
     *
     * Reads the permissions table (role_name => name rows). Static wildcard
     * grants ('*', 'prefix.*', '!revoked') stored there are preserved.
     */
    public function permissionsForRole(string $roleName): array
    {
        $db = $this->db();
        if ($db === null || !preg_match('/^[A-Za-z0-9_.\-]+$/', $roleName)) {
            return [];
        }

        try {
            $rows = $db->where('role_name', $roleName)->get('permissions')->result_array();
            $perms = [];
            foreach ($rows as $row) {
                if (isset($row['name']) && $row['name'] !== '') {
                    $perms[] = (string) $row['name'];
                }
            }
            return array_values(array_unique($perms));
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     *
     * Returns every role definition keyed by name, carrying 'title',
     * 'level' and 'inherits' (comma-separated child roles parsed into an
     * array) so RoleHierarchy can build the permission-expansion graph.
     */
    public function allRoles(): array
    {
        $db = $this->db();
        if ($db === null) {
            return [];
        }

        try {
            $rows = $db->order_by('level', 'ASC')->get('roles')->result_array();
        } catch (\Throwable) {
            return [];
        }

        $roles = [];
        foreach ($rows as $row) {
            $name = (string) ($row['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $def = [
                'title' => isset($row['title']) ? (string) $row['title'] : $name,
                'level' => (int) ($row['level'] ?? 99),
            ];
            $inherits = trim((string) ($row['inherits'] ?? ''));
            if ($inherits !== '') {
                $kids = array_values(array_filter(array_map('trim', explode(',', $inherits))));
                if ($kids !== []) {
                    $def['inherits'] = $kids;
                }
            }
            $roles[$name] = $def;
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     *
     * Loads raw ACL rule rows from the "acl" table and normalizes them to
     * the shape expected by Kodhe\Framework\Auth\Acl. The scope column is
     * JSON text; a subject of "role:<name>" is translated to the plain
     * role-name form the ACL matcher understands.
     */
    public function aclRules(): array
    {
        $db = $this->db();
        if ($db === null) {
            return [];
        }

        try {
            $rows = $db->order_by('priority', 'DESC')->get('acl')->result_array();
        } catch (\Throwable) {
            return [];
        }

        $rules = [];
        foreach ($rows as $row) {
            $scope = [];
            $rawScope = (string) ($row['scope'] ?? '');
            if ($rawScope !== '') {
                $decoded = json_decode($rawScope, true);
                if (is_array($decoded)) {
                    $scope = $decoded;
                }
            }

            $subject = (string) ($row['subject'] ?? '*');
            if (str_starts_with($subject, 'role:')) {
                $subject = substr($subject, 5);
            }

            $rules[] = [
                'target'   => (string) ($row['target'] ?? '*'),
                'subject'  => $subject,
                'effect'   => strtolower((string) ($row['effect'] ?? 'allow')) === 'deny' ? 'deny' : 'allow',
                'priority' => (int) ($row['priority'] ?? 0),
                'scope'    => $scope,
                'enabled'  => (bool) ($row['enabled'] ?? true),
                'expires'  => isset($row['expires']) && $row['expires'] !== null && $row['expires'] !== ''
                    ? (int) $row['expires']
                    : null,
            ];
        }

        return $rules;
    }

    /**
     * Resolve the active DB connection (CI super-object or kodhe() container).
     */
    private function db(): ?object
    {
        if (function_exists('kodhe')) {
            $app = kodhe();
            if (is_object($app) && isset($app->db) && is_object($app->db)) {
                return $app->db;
            }
        }

        if (function_exists('get_instance')) {
            $ci = get_instance();
            if (is_object($ci) && isset($ci->db) && is_object($ci->db)) {
                return $ci->db;
            }
        }

        return null;
    }
}
