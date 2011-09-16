<?php

include 'v.php';

restore_error_handler();
error_reporting(E_ALL);


// set path
$bearPath = realpath(dirname(__DIR__));
$bearVendorPath = "$bearPath/BEAR/vendors/PEAR";
$pandaPath = realpath(dirname(dirname(__DIR__)) . '/Panda');
$bearDemoPath = realpath(__DIR__ . '/apps/beardemo.local');

// set autoloder
$includePath = $bearPath . PATH_SEPARATOR . $bearVendorPath . PATH_SEPARATOR . $pandaPath . PATH_SEPARATOR . $bearDemoPath . PATH_SEPARATOR . get_include_path();
set_include_path($includePath);

spl_autoload_register('bearTestAutolodaer');
BEAR::set('page', new BEAR_Page_Cli(array()));
function bearTestAutolodaer($class) {
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    include_once $file;
}