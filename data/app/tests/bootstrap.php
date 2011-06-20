<?php
// set path
$appPath = realpath(__DIR__ . '/..');
// set autoloder
set_include_path($appPath . PATH_SEPARATOR . get_include_path());
require_once $appPath . '/App.php';

spl_autoload_register('bearTestAutolodaer');

$filter = PHP_CodeCoverage_Filter::getInstance();
$filter->removeDirectoryFromWhitelist($appPath . 'App/views');

function bearTestAutolodaer($class) {
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    require_once $file;
}
