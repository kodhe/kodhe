<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Env file loader (zero-dependency Dotenv)
|--------------------------------------------------------------------------
|
| Parses a Laravel-style .env file and pushes the values into the PHP
| environment via putenv() + $_ENV + $_SERVER, so both getenv('KEY') and
| $_ENV['KEY'] work everywhere in the framework and app configs.
|
| Real environment variables always take precedence (immutable style):
| existing keys are never overwritten by the file.
|
| This helper is loaded by bootstrap/app.php BEFORE the composer autoloader
| and before any application/config/*.php file runs, which is what makes
| .env values (DB_*, GOOGLE_CLIENT_ID, ...) visible to getenv() calls in
| those config files.
|
*/

if (!function_exists('kodhe_parse_env_file')) {
    /**
     * Parse the contents of a .env file into key => value pairs.
     *
     * @return array<string,string>
     */
    function kodhe_parse_env_file(string $contents): array
    {
        $vars = [];

        foreach (preg_split('/\r\n|\r|\n/', $contents) as $line) {
            $line = trim($line);

            // Blank lines and comments.
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            // Optional "export" prefix.
            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            $eq = strpos($line, '=');
            if ($eq === false) {
                continue; // Not a KEY=VALUE line; ignore silently.
            }

            $key = trim(substr($line, 0, $eq));
            if ($key === '' || !preg_match('/^[A-Za-z_][A-Za-z0-9_.]*$/', $key)) {
                continue;
            }

            $value = trim(substr($line, $eq + 1));

            if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                $quote = $value[0];
                $end   = strrpos($value, $quote);
                if ($end !== false && $end > 0) {
                    $raw = substr($value, 1, $end - 1);
                    $value = ($quote === '"')
                        ? str_replace(
                            ['\\n', '\\t', '\\"', '\\\\'],
                            ["\n", "\t", '"',  '\\'],
                            $raw
                        )
                        : $raw; // single quotes: verbatim
                }
            } else {
                // Unquoted value: strip trailing inline comment (" # ...").
                $hash = strpos($value, ' #');
                if ($hash !== false) {
                    $value = rtrim(substr($value, 0, $hash));
                }
            }

            $vars[$key] = $value;
        }

        return $vars;
    }
}

if (!function_exists('kodhe_load_env')) {
    /**
     * Load a .env file into the environment (existing real env vars win).
     *
     * @param string|null $path Absolute path to the env file. Null = caller
     *                          must resolve a default.
     *
     * @return array<string,string> The variables that were actually applied.
     */
    function kodhe_load_env(?string $path = null): array
    {
        if ($path === null || !is_file($path) || !is_readable($path)) {
            return [];
        }

        $applied = [];

        foreach (kodhe_parse_env_file((string) file_get_contents($path)) as $key => $value) {
            // Immutable: never clobber a variable already present in the
            // real environment (server/apache/docker set these first).
            if (getenv($key) !== false || isset($_ENV[$key]) || isset($_SERVER[$key])) {
                continue;
            }

            putenv($key . '=' . $value);
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
            $applied[$key] = $value;
        }

        return $applied;
    }
}
