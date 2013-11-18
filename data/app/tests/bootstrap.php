<?php
// for test
$_SERVER['bearmode'] = 100;

$loader = require dirname(__DIR__). '/vendor/autoload.php';
/** @var $loader \Composer\Autoload\ClassLoader */
$loader->add('', __DIR__);
$loader->register();

$appPath = realpath(__DIR__ . '/..');
set_include_path(get_include_path() . PATH_SEPARATOR . $appPath . PATH_SEPARATOR . $appPath . '/libs/pear/php' . PATH_SEPARATOR . $appPath  . '/libs/pear/php/BEAR' . PATH_SEPARATOR );

require_once $appPath . '/App.php';

// extension Xdebug
//$filter = PHP_CodeCoverage_Filter::getInstance();
//$filter->removeDirectoryFromWhitelist($appPath . 'App/views');
