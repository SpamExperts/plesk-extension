<?php

define('LIB_ROOT', realpath(__DIR__ . '/../../plib/library'));

// Mirror Plesk's autoloading for extension classes, so that the classes being
// mocked are loaded (PHPUnit can no longer mock classes that do not exist).
// E.g. Modules_SpamexpertsExtension_Plesk_Domain => plib/library/Plesk/Domain.php
spl_autoload_register(function ($class) {
    $prefix = 'Modules_SpamexpertsExtension_';
    if (0 !== strpos($class, $prefix)) {
        return;
    }

    $file = LIB_ROOT . '/' . str_replace('_', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
