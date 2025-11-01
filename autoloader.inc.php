<?php

const FILE_EXTENSIONS = [".class.php", ".interface.php"];

spl_autoload_register(function ($className) {
    $className = str_replace("\\", "/", $className);

    foreach (FILE_EXTENSIONS as $ext) {
        if (file_exists($className . $ext)) {
            require_once($className . $ext);
            return;
        }
    }

    echo "Failed to load: " . $className;
    die();
});
