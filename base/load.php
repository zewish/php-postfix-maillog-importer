<?php

define('AUTOLOAD_BASE_DIR', dirname(__DIR__));

/**
 * Autoloader
 */

function __autoload($name)
{
    if (!is_string($name)) {
        throw new AutloaderException('Invalid class specified for loading.');
    }

    $fileName = AUTOLOAD_BASE_DIR . '/' . str_replace('\\', '/', strtolower($name)) . '.php';
    if (!file_exists($fileName)) {
        throw new AutloaderException('File not found: "' . $fileName . '".');
    }

    require_once $fileName;
}

class AutloaderException extends Exception {}

/**
 * Configs loader
 */

function Config($cfgClass)
{
    static $configClasses = array();

    $cfgClass = 'Configs\\' . $cfgClass;
    if (!array_key_exists($cfgClass, $configClasses)) {
        $configClasses[$cfgClass] = new $cfgClass();
    }
    return $configClasses[$cfgClass];
}