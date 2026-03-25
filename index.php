<?php

declare(strict_types=1);


spl_autoload_register(function ($class) {
    $prefix = 'App\\Src\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$request = new \App\Src\Request($_GET, $_POST);


$renderer = new \App\Src\Renderer(__DIR__ . '/views/'); 

$app = new \App\Src\App($request, $renderer);

$app->run();