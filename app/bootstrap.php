<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/helpers.php';

// Autoload simples para classes core, models, services e repositories
spl_autoload_register(function ($class) {
    $directories = ['core', 'models', 'services', 'repositories'];
    foreach ($directories as $dir) {
        $file = __DIR__ . '/' . $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

Session::start();
Csrf::requireToken(); // Valida automaticamente requisições POST com csrf_token
