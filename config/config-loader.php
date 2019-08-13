<?php

return function (string $directory = __DIR__) {
    $files = scandir($directory);

    $files = preg_grep('/^([^.])/', $files);

    $files = array_values($files);

    $map = [];
    foreach ($files as $file) {
        $fullPath = $directory . DIRECTORY_SEPARATOR . $file;

        if ($file === __FILE__) {
            continue;
        }

        if (pathinfo($fullPath, PATHINFO_FILENAME) === 'settings') {
            $map['settings'] = require $fullPath;

            continue;
        }

        $map['config'] = require $fullPath;
    }

    return $map;
};