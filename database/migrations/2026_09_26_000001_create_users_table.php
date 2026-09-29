<?php

declare(strict_types=1);

use Kodhe\Framework\Database\Loader;

/**
 * Migration: create users table (example for kodhe/auth).
 *
 * Columns consumed by the guard are configured in
 * application/config/auth.php: identifier_column=email,
 * password_column=password, remember_column=remember_token.
 */
return new class {

    public function up(): void
    {
        $forge = Loader::dbforge(null, true);

        $forge->add_field([
            'id'             => ['type' => 'INT', 'constraint' => 9, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 190],
            'password'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'remember_token' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => TRUE],
            'created_at'     => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'     => ['type' => 'DATETIME', 'null' => TRUE],
        ]);

        $forge->add_key('id', TRUE);
        $forge->add_key('email');
        $forge->create_table('users', TRUE);
    }

    public function down(): void
    {
        $forge = Loader::dbforge(null, true);
        $forge->drop_table('users', TRUE);
    }
};
