<?php

\spl_autoload_register(function ($class) {

    if (stripos($class, 'pechenki\ChatLifeForTelegram') !== 0) {
    return;
    }


    $classFile = str_replace('\\', '/', substr($class, strlen('pechenki\ChatLifeForTelegram') + 1) . '.php');

    include_once __DIR__ . '/' . $classFile; 
});
