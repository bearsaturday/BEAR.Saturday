<?php

include 'v.php';

restore_error_handler();
error_reporting(E_ALL);


// set path
$bearPath = realpath(__DIR__ . '/../');
$bearDemoPath = '/usr/local/app/bear.demo';

// set autoloder
set_include_path($bearPath . PATH_SEPARATOR . $bearDemoPath . PATH_SEPARATOR . get_include_path());

spl_autoload_register('bearTestAutolodaer');
BEAR::set('page', new BEAR_Page_Cli(array()));
function bearTestAutolodaer($class) {
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    include_once $file;
}