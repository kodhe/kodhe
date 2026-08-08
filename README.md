# Kodhe Framework

A lightweight, powerful PHP framework built on top of CodeIgniter with modern features and clean architecture.

## Features

- **MVC Architecture** - Clean separation of concerns with Model-View-Controller pattern
- **Lightweight & Fast** - Optimized for performance with minimal overhead
- **Secure by Default** - Built-in security features including XSS protection, CSRF tokens, and input validation
- **Powerful Database Support** - Multiple database drivers and query builder
- **RESTful API Ready** - Easy-to-use routing for building REST APIs
- **Multi-language Support** - Built-in internationalization (i18n) capabilities
- **Modern PHP** - Requires PHP 7.2+ with namespace support

## Requirements

- PHP >= 7.2
- MySQL, PostgreSQL, SQLite, or other supported database
- Web server (Apache, Nginx, etc.)
- Composer (for dependency management)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd kodhe
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

Copy the environment configuration file and adjust settings:

```bash
cp .env.example .env
```

### 4. Set Permissions

Ensure the following directories are writable:

```bash
chmod -R 755 storage/
chmod -R 755 public/
```

### 5. Configure Database

Edit `application/config/database.php` with your database credentials:

```php
$db['default'] = [
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'your_database',
    'dbdriver' => 'mysqli',
];
```

## Directory Structure

```
kodhe/
├── application/          # Application directory
│   ├── config/          # Configuration files
│   ├── controllers/     # Controller classes
│   ├── core/            # Core framework files
│   ├── helpers/         # Helper functions
│   ├── language/        # Language files
│   ├── libraries/       # Custom libraries
│   ├── middlewares/     # Middleware classes
│   ├── models/          # Model classes
│   ├── modules/         # Modular extensions
│   ├── routes/          # Route definitions
│   ├── third_party/     # Third-party libraries
│   └── views/           # View templates
├── bootstrap/           # Bootstrap files
├── public/              # Public web root
│   ├── assets/          # CSS, JS, images
│   └── index.php        # Entry point
├── storage/             # Storage directory (logs, cache, etc.)
├── tests/               # Unit tests
├── composer.json        # Composer dependencies
└── README.md            # This file
```

## Usage

### Running the Application

Start the development server:

```bash
php -S localhost:8000 -t public
```

Or configure your web server to point to the `public/` directory.

### Creating a Controller

```php
<?php namespace App\Controllers;

class Home extends \CI_Controller {
    
    public function index()
    {
        $data['title'] = 'Home Page';
        return $this->load->view('home', $data);
    }
}
```

### Creating a Model

```php
<?php namespace App\Models;

class User extends \CI_Model {
    
    public function getAllUsers()
    {
        return $this->db->get('users')->result();
    }
}
```

### Defining Routes

Routes are defined in `application/routes/`:

```php
$routes->get('/', 'Home::index');
$routes->get('/about', 'About::index');
$routes->post('/api/users', 'Api\Users::create');
```

### Using Languages

Load language files in your controller:

```php
$this->lang->load('welcome', 'english');
echo $this->lang->line('welcome_message');
```

## Testing

Run unit tests using PHPUnit:

```bash
cd tests
composer install
./vendor/bin/phpunit
```

## Configuration

### Environment Settings

Set the environment in `public/index.php` or via `.env`:

```php
define('ENVIRONMENT', 'development'); // or 'production', 'testing'
```

### Autoloading

Configure autoloading in `application/config/autoload.php`:

```php
$autoload['libraries'] = ['database', 'session'];
$autoload['helpers'] = ['url', 'language'];
```

## Security

- Always validate and sanitize user input
- Use CSRF protection for forms
- Escape output to prevent XSS attacks
- Use prepared statements for database queries
- Keep dependencies updated

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT License](LICENSE).

## Support

For issues and questions:
- Check the documentation
- Open an issue on GitHub
- Contact the maintainers

## Credits

Built with ❤️ using the KaryaKode Framework