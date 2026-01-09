<?php
spl_autoload_register(function ($class) {

    // Example: Zxing\BinaryBitmap
    $class = str_replace('\\', '/', $class);

    // Remove leading Zxing/
    $class = str_replace('Zxing/', '', $class);

    $file = __DIR__ . '/Zxing/' . $class . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
