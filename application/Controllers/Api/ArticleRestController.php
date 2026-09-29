<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use Kodhe\Framework\Http\Controllers\RESTController;
use Kodhe\Framework\Http\JsonResponse;
use App\Models\Article_model;

/**
 * Example REST API controller for the "articles" resource.
 *
 * Extends Kodhe\Framework\Http\Controllers\RESTController, which provides:
 *   - a consistent JSON envelope ({success, data, meta} / {success:false, error})
 *   - response helpers: respond(), respondCreated(), respondNoContent(),
 *     respondNotFound(), respondValidation(), respondError()
 *
 * Routes are registered in application/routes/api.php with
 * Route::apiResource('articles', ...) inside an 'api'-prefixed group
 * carrying the 'api' middleware group.
 *
 * Endpoints:
 *   GET    /api/articles            -> index()   (list, ?per_page&page pagination)
 *   POST   /api/articles            -> store()
 *   GET    /api/articles/{article}  -> show($id)
 *   PUT    /api/articles/{article}  -> update($id)
 *   PATCH  /api/articles/{article}  -> patch($id)  (partial update)
 *   DELETE /api/articles/{article}  -> destroy($id)
 */
class ArticleRestController extends RESTController
{
    /**
     * Human readable name used in default error messages.
     */
    protected string $resourceName = 'article';

    /**
     * Mass-assignable columns (mirrors Article_model::$allowedFields).
     */
    private const FILLABLE = ['title', 'slug', 'body', 'published'];

    /**
     * Lazily resolved ORM instance.
     *
     * @var Article_model|null
     */
    private $articles = null;

    public function __construct()
    {
        parent::__construct();

        // Modern stack: make sure the DB connection is registered. Safe no-op
        // when the legacy pipeline already loaded it.
        if (class_exists(\Kodhe\Framework\Database\Loader::class)) {
            \Kodhe\Framework\Database\Loader::database('', true, true);
        }

        // The model now lives in the App\Models namespace (application/models/
        // Article_model.php) and resolves automatically through the PSR-4
        // "App\\" => application/ autoload map — no manual require needed.
    }

    /**
     * @return Article_model
     */
    protected function model()
    {
        if ($this->articles === null) {
            $this->articles = new Article_model();
        }

        return $this->articles;
    }

    // ------------------------------------------------------------------
    // GET /api/articles
    // ------------------------------------------------------------------

    public function index(): JsonResponse
    {
        $request = $this->getRequest();

        // Optional filters: ?q= (title search) & ?published=0|1
        $query = $this->model();

        if (($search = $request->input('q')) !== null && $search !== '') {
            $query = $query->like('title', $search);
        }

        $published = $request->input('published');
        if ($published !== null && $published !== '') {
            $query = $query->where('published', (int) $published);
        }

        // Pagination: ?per_page=&page=
        $perPage = max(1, min(100, (int) $request->input('per_page', 15)));
        $page    = max(1, (int) $request->input('page', 1));

        $total = $this->freshQueryCount();

        $items = $query->orderBy('id', 'DESC')
                       ->findAll($perPage, ($page - 1) * $perPage);

        return $this->respond($items, [
            'total'     => $total,
            'per_page'  => $perPage,
            'page'      => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ]);
    }

    // ------------------------------------------------------------------
    // POST /api/articles
    // ------------------------------------------------------------------

    public function store(): JsonResponse
    {
        $data = $this->validatePayload($this->allInput(), required: true);

        if (isset($data['errors'])) {
            return $this->respondValidation($data['errors']);
        }

        $payload = $this->fillableOnly($data['values']);
        $payload['slug'] = $this->makeSlug($payload['slug'] ?? $payload['title']);

        $id = $this->model()->insert($payload);

        return $this->respondCreated($this->model()->find($id));
    }

    // ------------------------------------------------------------------
    // GET /api/articles/{id}
    // ------------------------------------------------------------------

    public function show($id): JsonResponse
    {
        $article = $this->model()->find($id);

        if ($article === null) {
            return $this->respondNotFound($id);
        }

        return $this->respond($article);
    }

    // ------------------------------------------------------------------
    // PUT /api/articles/{id}  (full replace of fillable fields)
    // ------------------------------------------------------------------

    public function update($id): JsonResponse
    {
        $article = $this->model()->find($id);

        if ($article === null) {
            return $this->respondNotFound($id);
        }

        $data = $this->validatePayload($this->allInput(), required: true);

        if (isset($data['errors'])) {
            return $this->respondValidation($data['errors']);
        }

        $payload = $this->fillableOnly($data['values']);
        $payload['slug'] = $this->makeSlug($payload['slug'] ?? $payload['title']);

        $this->model()->update($payload, $id);

        return $this->respond($this->model()->find($id));
    }

    // ------------------------------------------------------------------
    // PATCH /api/articles/{id}  (partial update)
    // ------------------------------------------------------------------

    public function patch($id): JsonResponse
    {
        $article = $this->model()->find($id);

        if ($article === null) {
            return $this->respondNotFound($id);
        }

        $data = $this->validatePayload($this->allInput(), required: false);

        if (isset($data['errors'])) {
            return $this->respondValidation($data['errors']);
        }

        $payload = $this->fillableOnly($data['values']);

        if (empty($payload)) {
            return $this->respondValidation(['payload' => ['No fillable fields provided.']]);
        }

        if (isset($payload['title']) || isset($payload['slug'])) {
            $base = $payload['slug'] ?? $payload['title'];
            $current = is_array($article) ? ($article['slug'] ?? null) : ($article->slug ?? null);
            if ($base !== $current) {
                $payload['slug'] = $this->makeSlug($base);
            }
        }

        $this->model()->update($payload, $id);

        return $this->respond($this->model()->find($id));
    }

    // ------------------------------------------------------------------
    // DELETE /api/articles/{id}
    // ------------------------------------------------------------------

    public function destroy($id): JsonResponse
    {
        $article = $this->model()->find($id);

        if ($article === null) {
            return $this->respondNotFound($id);
        }

        $this->model()->delete($id);

        return $this->respondNoContent();
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Merge raw/form input + JSON body (JSON body wins on key conflicts).
     */
    protected function allInput(): array
    {
        $request = $this->getRequest();

        return array_merge(
            (array) $request->input(),
            (array) $request->json()
        );
    }

    /**
     * Very small validation layer (kept dependency-free on purpose — this is
     * an example of the REST plumbing, not of the validation component).
     *
     * @return array{values?: array, errors?: array<string, list<string>>}
     */
    protected function validatePayload(array $input, bool $required): array
    {
        $errors = [];

        if ($required && trim((string) ($input['title'] ?? '')) === '') {
            $errors['title'][] = 'The title field is required.';
        }

        if (isset($input['title']) && mb_strlen((string) $input['title']) > 255) {
            $errors['title'][] = 'The title may not be greater than 255 characters.';
        }

        if (array_key_exists('published', $input)
            && !in_array((string) $input['published'], ['0', '1', 'true', 'false', '', 'null'], true)) {
            $errors['published'][] = 'The published field must be 0 or 1.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return ['values' => $input];
    }

    /**
     * Keep only the whitelisted columns.
     */
    protected function fillableOnly(array $input): array
    {
        $out = [];

        foreach (self::FILLABLE as $field) {
            if (array_key_exists($field, $input)) {
                $out[$field] = $input[$field];
            }
        }

        if (array_key_exists('published', $out)) {
            $out['published'] = filter_var($out['published'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        return $out;
    }

    protected function makeSlug(string $value): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $value), '-'));

        return $slug !== '' ? $slug : 'untitled-' . date('YmdHis');
    }

    /**
     * Total rows matching the current request filters, counted on a fresh
     * builder so the listing chain stays untouched.
     */
    private function freshQueryCount(): int
    {
        $request = $this->getRequest();
        $count = new Article_model();

        if (($search = $request->input('q')) !== null && $search !== '') {
            $count = $count->like('title', $search);
        }

        $published = $request->input('published');
        if ($published !== null && $published !== '') {
            $count = $count->where('published', (int) $published);
        }

        return (int) $count->count();
    }
}
