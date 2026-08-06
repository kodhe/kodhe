<?php

    define('CI_VERSION', '3.2.0-dev');
    define('BASEPATH', SYSPATH);

/*
 * ------------------------------------------------------
 *  Load the autoloader and register it
 * ------------------------------------------------------
 */
    // Include composer autoloader
    $autoloadPath = __DIR__ . DIRECTORY_SEPARATOR. '..'.DIRECTORY_SEPARATOR.'vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        header('HTTP/1.1 503 Service Unavailable', true, 503);
        echo 'Your vendor/autoload.php file does not appear to be set correctly.';
        exit(3);
    } else {
        require $autoloadPath;
    }

    if(file_exists(APPPATH . 'config/constants.php')) {
        // load user configurable constants
        $constants = require APPPATH . 'config/constants.php';
    } else {
        $constants = [];
    }

    if (file_exists(BASEPATH . '/user/config/constants.php')) {
        $user_constants = include BASEPATH . '/user/config/constants.php';
        if (is_array($user_constants)) {
            $constants = array_merge($constants, $user_constants);
        }
    }

    if(is_array($constants) && !empty($constants)) {
        foreach ($constants as $k => $v) {
            defined($k) || define($k, $v);
        }
    }

    Kodhe\Framework\Support\Autoloader::getInstance()->addPrefix('App', APPPATH)->register();
    

/*
 * ------------------------------------------------------
 *  Load the environment
 * ------------------------------------------------------
 */
    try {
        if (file_exists(__DIR__ .'/../.env.php')) {
            $dotenv = Kodhe\Dependency\Dotenv\Dotenv::createImmutable(__DIR__ .'/../', '.env.php');
            $dotenv->load();
            // force the installer/updater?
            defined('INSTALL_MODE') || define('INSTALL_MODE', ($_ENV['EE_INSTALL_MODE'] ?? false) === 'TRUE');
        }
    } catch (\Exception $e) {

    }


/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
    // ci 3.0.0-dev
    //set_error_handler('_exception_handler');

    set_error_handler('_error_handler');
	set_exception_handler('_exception_handler');
	register_shutdown_function('_shutdown_handler');
    

    $charset = strtoupper(config_item('charset'));
    ini_set('default_charset', $charset);

    if (extension_loaded('mbstring'))
    {
        define('MB_ENABLED', TRUE);
        // mbstring.internal_encoding is deprecated starting with PHP 5.6
        // and it's usage triggers E_DEPRECATED messages.
        @ini_set('mbstring.internal_encoding', $charset);
        // This is required for mb_convert_encoding() to strip invalid characters.
        // That's utilized by CI_Utf8, but it's also done for consistency with iconv.
        mb_substitute_character('none');
    }
    else
    {
        define('MB_ENABLED', FALSE);
    }

    // There's an ICONV_IMPL constant, but the PHP manual says that using
    // iconv's predefined constants is "strongly discouraged".
    if (extension_loaded('iconv'))
    {
        define('ICONV_ENABLED', TRUE);
        // iconv.internal_encoding is deprecated starting with PHP 5.6
        // and it's usage triggers E_DEPRECATED messages.
        @ini_set('iconv.internal_encoding', $charset);
    }
    else
    {
        define('ICONV_ENABLED', FALSE);
    }

    if (is_php('5.6'))
    {
        ini_set('php.internal_encoding', $charset);
    }
    

    if (
        defined('PREG_BAD_UTF8_ERROR')				// PCRE must support UTF-8
        && (ICONV_ENABLED === TRUE OR MB_ENABLED === TRUE)	// iconv or mbstring must be installed
        && strtoupper(config_item('charset')) === 'UTF-8'	// Application charset must be UTF-8
        )
    {
        define('UTF8_ENABLED', TRUE);
        log_message('debug', 'UTF-8 Support Enabled');
    }
    else
    {
        define('UTF8_ENABLED', FALSE);
        log_message('debug', 'UTF-8 Support Disabled');
    }

/*
 * ------------------------------------------------------
 *  Check for the installer if we're booting the CP
 * ------------------------------------------------------
 */
    $container = new Kodhe\Framework\Container\Container();

    // Create application instance (dengan struktur baru)
    $app = Kodhe\Framework\Foundation\Application::create($container);

/*
 * ------------------------------------------------------
 *  Boot the core - Sekarang dilakukan via Application::run()
 * ------------------------------------------------------
 */
    // Tidak perlu memanggil boot() secara manual karena
    // Application::run() akan memanggilnya secara otomatis

/*
 * ------------------------------------------------------
 *  Set config items from the index.php file
 * ------------------------------------------------------
 */
    if (isset($assign_to_config)) {
        // Pindahkan method overrideConfig ke Kernel atau Application
        $kernel = $app->getKernel();
        // Perlu ditambahkan method overrideConfig di Kernel jika diperlukan
        // $kernel->overrideConfig($assign_to_config);
    }

/*
 * ------------------------------------------------------
 *  Set routing overrides from the index.php file
 * ------------------------------------------------------
 */
    if (isset($routing)) {
        // Pindahkan method overrideRouting ke Kernel atau Application
        $kernel = $app->getKernel();
        // Perlu ditambahkan method overrideRouting di Kernel jika diperlukan
        // $kernel->overrideRouting($routing);
    }

/*
 * ------------------------------------------------------
 *  Create global helper functions
 *
 *  Using `CI` for the global name, just in case someone
 *  is relying on that instead of get_instance()
 * ------------------------------------------------------
 */

    // Pastikan fungsi get_instance belum didefinisikan
    if (!function_exists('get_instance')) {
        function &get_instance()
        {
            // Dengan struktur baru, kita perlu akses facade melalui Application
            $app = Kodhe\Framework\Foundation\Application::create();
            $kernel = $app->getKernel();
            $facade = $kernel->getFacade();
            return $facade;
        }
    }


    // Pastikan fungsi app sudah didefinisikan
    if (!function_exists('app')) {
        function &app($dep = null)
        {
            // Alternatif: langsung return Application instance
            // Tapi untuk kompatibilitas, kita tetap pakai facade
            $facade =& get_instance();
            if (isset($dep) && isset($facade->di)) {
                $args = func_get_args();
                $call_func = call_user_func_array(array($facade->di, 'make'), $args);
                return $call_func;
            }
            
            return $facade;
        }
    }

    // Pastikan fungsi kodhe sudah didefinisikan
    if (!function_exists('kodhe')) {
        function &kodhe($dep = null)
        {
            return app($dep);
        }
    }


/*
 * ------------------------------------------------------
 *  Parse the request
 * ------------------------------------------------------
 */
    $request = Kodhe\Framework\Http\Request::fromGlobals();

/*
 * ------------------------------------------------------
 *  Run the request and get a response
 * ------------------------------------------------------
 */
    try {
        // Gunakan method run() dari Application
        $response = $app->run($request);
    } catch (ParseError $ex) {
        show_exception($ex);
    } catch (Error $ex) {
        show_exception($ex);
    } catch (\Exception $ex) {
        show_exception($ex);
    }

/*
 * ------------------------------------------------------
 *  Send the response
 * ------------------------------------------------------
 */
    if ($response) {
        $response->send();
    }