<?php

spl_autoload_register(function ($className) {
    $prefix = 'galapagos\\';
    $basePath = __DIR__.'/src/';
    if (strncmp($prefix, $className, strlen($prefix)) === 0) {
        require $basePath.str_replace('\\', '/', substr($className, strlen($prefix))).'.php';
    }
});

require __DIR__.'/src/core.php';
require __DIR__.'/src/php54.php';
