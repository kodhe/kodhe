<?php

declare(strict_types=1);

namespace App\Models;

use Kodhe\Framework\Database\Model;

/**
 * Article model (example for the kodhe REST API controller).
 *
 * Namespaced under App\Models so it resolves through the PSR-4 autoload map
 * ("App\\" => application/) declared in composer.json — no manual require /
 * global class name needed anymore.
 *
 * Uses the modern Kodhe ORM (kodhe/database). The table is created by
 * database/migrations/2026_09_25_073112_create_articles_table.php:
 * id, title, slug, body, published, created_at, updated_at.
 */
class Article_model extends Model
{
    protected $table = 'articles';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useTimestamps = true;

    /**
     * Mass-assignment whitelist — the REST controller passes request input
     * straight into insert()/update(), so only these columns are writable.
     */
    protected $allowedFields = ['title', 'slug', 'body', 'published'];

    // No custom constructor needed anymore: the framework Model now arms
    // the query builder on demand (Model::_armBuilder()) instead of pushing
    // FROM onto the shared connection in its constructor, which previously
    // caused "Not unique table/alias: 'articles'" (MySQL/PDO) and
    // "no tables specified" (SQLite).
}
