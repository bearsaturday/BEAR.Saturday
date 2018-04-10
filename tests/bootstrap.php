<?php

restore_error_handler();
date_default_timezone_set('Asia/Tokyo');

// set path
$basePath = dirname(__DIR__);
$bearDemoPath = __DIR__ . '/sites/beardemo.local';

// set autoloder
set_include_path($basePath . PATH_SEPARATOR . $bearDemoPath . PATH_SEPARATOR . get_include_path());
require_once 'vendor/autoload.php';

spl_autoload_register('bearTestAutolodaer');
if (!BEAR::exists('page')) {
    BEAR::set('page', new BEAR_Page_Cli(array()));
}
function bearTestAutolodaer($class) {
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    include_once $file;
}