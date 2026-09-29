<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Environment (.env) loader
|--------------------------------------------------------------------------
|
| Zero-dependency .env reader for the kodhe project. Loaded very early by
| bootstrap/app.php — before the composer autoloader registers helper files
| and before application/config/*.php files call getenv() — so values from
| the project's .env file are visible to every config (database, socialite
| credentials, session/cookie names, ...).
|
| Supported syntax:
|   KEY=value            plain value (trimmed)
|   KEY="quoted value"   single/double quotes stripped, escapes \n \t \" \\
|   KEY='literal'        single-quoted values kept verbatim
|   export KEY=value     optional "export" prefix is ignored
|   # comment            full-line comments and blank lines are skipped
|   inline trailing comments are honoured for UNQUOTED values (# ...)
|
| Behaviour:
|   - Existing real environment variables always WIN (like Dotenv::
|     createImmutable): the .env file only fills gaps.
|   - Values are pushed to getenv()/$_ENV/$_SERVER so both style()s work.
|   - A missing .env file is not an error (defaults in config apply).
|
| Usage: copy .env.example to .env and fill in your secrets. Never commit
| the real .env file.
|
*/

return [
    // Absolute path (or path relative to APPPATH/project root) of the env
    // file to load. Null = "<project root>/.env".
    'path' => null,

    // Set false to disable loading entirely without deleting this file.
    'enabled' => true,
];
