<?php

spl_autoload_register(function ($className) {
    $prefix = 'galapagos\\';
    $basePath = __DIR__.'/src/';
    if (strncmp($prefix, $className, strlen($prefix)) === 0) {
        require $basePath.str_replace('\\', '/', substr($className, strlen($prefix))).'.php';
    }
});
