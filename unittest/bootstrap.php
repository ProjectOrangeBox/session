<?php

// the project root path
define('__ROOT__', realpath(__DIR__ . '/../../../../'));
// the htdocs path
define('__WWW__', realpath(__DIR__ . '/../../../../htdocs'));

// the environment variables seeded with the phpunit environment
$_ENV = array_replace_recursive($_ENV, ['ENVIRONMENT' => 'phpunit']);

define('ORANGEDIR', realpath(__DIR__ . '/../../framework/src'));

// The framework's global helpers (logMsg, container, isLogEnabled, ...) are normally
// loaded at runtime by Application::preContainer() via dynamic include_once, not
// through composer's autoloader. Load them here so tests that instantiate framework
// services have them available - mirrors vendor/orange/framework/unittest/bootstrap.php.
require ORANGEDIR . '/helpers/helpers.php';
require ORANGEDIR . '/helpers/errors.php';
require ORANGEDIR . '/helpers/wrappers.php';

// include the composer autoloader
require __DIR__ . '/../../../autoload.php';
