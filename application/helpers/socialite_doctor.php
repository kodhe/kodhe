<?php

declare(strict_types=1);

use Kodhe\Framework\Socialite\Http\CurlClient;
use Kodhe\Framework\Socialite\Session\ArrayStateStore;
use Kodhe\Framework\Socialite\Two\FacebookProvider;
use Kodhe\Framework\Socialite\Two\GithubProvider;
use Kodhe\Framework\Socialite\Two\GitlabProvider;
use Kodhe\Framework\Socialite\Two\GoogleProvider;
use Kodhe\Framework\Socialite\Two\LinkedinProvider;

if (!function_exists('socialite_doctor_report')) {
    /**
     * Build a full diagnostic report of the social-login stack for the
     * kodhe project: env file, config wiring, per-provider credentials,
     * endpoints reachability and local prerequisites (session store,
     * redirect URI derivation). Pure read-only; never sends secrets.
     *
     * @return array<string,mixed>
     */
    function socialite_doctor_report(): array
    {
        $root = dirname(__DIR__, 2); // <project>/kodhe
        $report = [
            'generated_at' => date('Y-m-d H:i:s'),
            'checks'       => [],
            'providers'    => [],
        ];

        $add = static function (string $group, string $name, string $status, string $hint = '') use (&$report): void {
            $report['checks'][] = compact('group', 'name', 'status', 'hint');
        };

        /* ----------------------------------------------------------------
         | 1. Environment (.env)
         | ---------------------------------------------------------------- */
        $envFile = $root . '/.env';
        if (is_file($envFile)) {
            $add('environment', '.env file', 'ok', basename($envFile) . ' ditemukan di root project.');
        } else {
            $add('environment', '.env file', 'warn',
                'Tidak ada .env — salin .env.example menjadi .env lalu isi kredensial.');
        }

        require_once $root . '/application/helpers/env_helper.php';
        $appliedEnv = is_file($envFile) ? count(kodhe_parse_env_file((string) file_get_contents($envFile))) : 0;
        $add('environment', 'vars in .env', $appliedEnv > 0 ? 'ok' : 'info',
            $appliedEnv . ' baris KEY=VALUE terparse.');

        /* ----------------------------------------------------------------
         | 2. Config files
         | ---------------------------------------------------------------- */
        $socialiteCfg = (array) (require $root . '/application/config/socialite.php');
        $bridgeCfg    = (array) (require $root . '/application/config/socialite_auth.php');

        $add('config', 'socialite.php', isset($socialiteCfg['providers']) ? 'ok' : 'fail',
            'Harus mengembalikan array dengan key providers.');
        $add('config', 'socialite_auth.php', !empty($bridgeCfg['providers']) ? 'ok' : 'fail',
            'Daftar providers UI kosong?');

        // base_url drives the derived redirect URIs shown to the user.
        // Read it defensively: config_item() needs a booted framework, so
        // fall back to parsing application/config/app.php directly.
        $baseUrl = '';
        if (function_exists('config_item')) {
            try {
                foreach (['base_url', 'url'] as $k) {
                    $v = @config_item($k);
                    if (is_string($v) && $v !== '') { $baseUrl = rtrim($v, '/'); break; }
                }
            } catch (\Throwable) {
                $baseUrl = '';
            }
        }
        if ($baseUrl === '' && is_file($root . '/application/config/app.php')) {
            $appCfg = (array) (require $root . '/application/config/app.php');
            foreach (['base_url', 'url'] as $k) {
                $v = getenv(strtoupper($k)) ?: ($appCfg[$k] ?? null);
                if (is_string($v) && $v !== '') { $baseUrl = rtrim($v, '/'); break; }
            }
        }
        $add('config', 'app base_url', $baseUrl !== '' ? 'ok' : 'warn',
            $baseUrl !== '' ? $baseUrl : 'Kosong — callback_uri akan fallback ke path relatif.');

        /* ----------------------------------------------------------------
         | 3. Per-provider status
         | ---------------------------------------------------------------- */
        $classes = [
            'google'   => GoogleProvider::class,
            'github'   => GithubProvider::class,
            'facebook' => FacebookProvider::class,
            'gitlab'   => GitlabProvider::class,
            'linkedin' => LinkedinProvider::class,
        ];

        $http = new CurlClient(5); // short timeout: this is a ping, not a download

        foreach ((array) ($socialiteCfg['providers'] ?? []) as $name => $cfg) {
            $enabled  = (bool) ($cfg['enabled'] ?? true);
            $clientId = trim((string) ($cfg['client_id'] ?? ''));
            $secret   = trim((string) ($cfg['client_secret'] ?? ''));
            $uiList   = in_array($name, (array) ($bridgeCfg['providers'] ?? []), true);

            if (!$enabled) {
                $state = 'disabled';
            } elseif ($clientId === '' || $secret === '') {
                $state = 'unconfigured';
            } else {
                $state = 'ready';
            }

            $row = [
                'name'          => $name,
                'label'         => (string) (($bridgeCfg['labels'][$name] ?? ucfirst($name))),
                'state'         => $state,
                'in_ui'         => $uiList,
                'client_id_preview' => $clientId === '' ? '' : substr($clientId, 0, 6) . '…' . strlen($clientId),
                'secret_present'=> $secret !== '',
                'scopes'        => (array) ($cfg['scopes'] ?? []),
                'endpoints'     => [],
            ];

            // Derive real authorize/token/userinfo URLs by resolving the
            // driver through the Manager (no network involved yet).
            if (isset($classes[$name])) {
                try {
                    $manager = new \Kodhe\Framework\Socialite\Manager([
                        'callback_uri' => ($baseUrl ?: '/') . 'auth/socialite/callback',
                        'providers'    => [$name => array_merge($cfg, ['enabled' => true])],
                    ]);
                    $manager->setStateStore(new ArrayStateStore());
                    $manager->setHttpClient($http);
                    /** @var object $provider */
                    $provider = $manager->driver($name);

                    foreach (['getAuthUrl', 'getTokenUrl', 'getUserInfoUrl'] as $m) {
                        if (method_exists($provider, $m)) {
                            try {
                                $row['endpoints'][lcfirst(preg_replace('/^get/', '', $m))] = (string) $provider->{$m}();
                            } catch (\Throwable) {
                                // endpoints built lazily/require request ctx; skip
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $row['resolve_error'] = $e->getMessage();
                }

                // Network reachability ping (HEAD/GET on the authorize page —
                // public URL, contains no secret). Only when configured.
                if ($state === 'ready' && !empty($row['endpoints']['authUrl'])) {
                    $host = parse_url($row['endpoints']['authUrl'], PHP_URL_HOST) ?: '';
                    if ($host !== '') {
                        try {
                            $res = $http->request('GET', 'https://' . $host . '/', ['allow_redirects' => false, 'timeout' => 4]);
                            $code = (int) ($res['status'] ?? 0);
                            $row['reachable'] = $code > 0 && $code < 500;
                            $row['http_status'] = $code;
                        } catch (\Throwable $e) {
                            $row['reachable'] = false;
                            $row['http_error'] = $e->getMessage();
                        }
                    }
                }
            } else {
                $row['note'] = 'OAuth 1.0a (Twitter/X) — tidak punya endpoint authorize publik untuk diping; cek consumer key/secret.';
            }

            $report['providers'][] = $row;

            $add('providers', $name, match ($state) {
                'ready'        => ($row['reachable'] ?? true) ? 'ok' : 'fail',
                'unconfigured' => 'warn',
                default        => 'info',
            }, match ($state) {
                'ready'        => empty($row['reachable']) ? ('Host tidak terjangkau: ' . ($row['http_error'] ?? '')) : 'Siap dipakai.',
                'unconfigured' => 'Isi client_id/client_secret di .env.',
                default        => 'Dinonaktifkan via config/env.',
            });
        }

        /* ----------------------------------------------------------------
         | 4. Routes registered
         | ---------------------------------------------------------------- */
        $routes = (array) (require $root . '/application/routes/web.php');
        $flat   = (string) json_encode($routes);
        // web.php uses the fluent router ('/auth/socialite/{provider}' +
        // ->name('socialite.redirect')); legacy CI routes use (:any).
        $hasStart = str_contains($flat, 'auth/socialite/{provider}')
                 || str_contains($flat, 'socialite.redirect')
                 || str_contains($flat, 'auth/socialite/(:any)');
        $hasCb    = str_contains($flat, 'socialite.callback')
                 || str_contains($flat, 'auth/socialite/callback/(:any)')
                 || str_contains($flat, 'auth/socialite/{provider}/callback');
        $add('routes', 'start route',    $hasStart ? 'ok' : 'fail', 'GET /auth/socialite/{provider}');
        $add('routes', 'callback route', $hasCb ? 'ok' : 'fail', 'GET /auth/socialite/callback/{provider}');

        /* ----------------------------------------------------------------
         | 5. Session + CSRF state store
         | ---------------------------------------------------------------- */
        $add('runtime', 'PHP sessions', is_dir(ini_get('session.save_path') ?: '/tmp') ? 'ok' : 'warn',
            'save_path=' . (ini_get('session.save_path') ?: '/tmp (default)'));
        $add('runtime', 'ext-curl', extension_loaded('curl') ? 'ok' : 'warn',
            extension_loaded('curl') ? 'CurlClient aktif.' : 'Fallback ke PHP streams (lebih lambat).');
        $add('runtime', 'random_bytes', function_exists('random_bytes') ? 'ok' : 'fail',
            'Dipakai untuk CSRF state OAuth.');

        return $report;
    }
}
