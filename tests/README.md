# Unit Tests

This directory contains unit tests for the Kodhe project.

## Requirements

- PHP >= 7.2
- PHPUnit >= 8.0

## Installation

1. Install dependencies:
```bash
cd tests
composer install
```

## Running Tests

From the `tests` directory, run:
```bash
./vendor/bin/phpunit
```

Or from the project root:
```bash
cd tests && ./vendor/bin/phpunit
```

## Test Structure

- `Controllers/` - Tests for controllers
- `Models/` - Tests for models (to be added)
- `Libraries/` - Tests for libraries (to be added)
- `Helpers/` - Tests for helpers (to be added)

## Writing Tests

Tests follow PHPUnit conventions. Example:

```php
<?php
namespace App\Tests\Controllers;

use PHPUnit\Framework\TestCase;

class MyControllerTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }
}
```
