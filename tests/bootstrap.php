<?php

restore_error_handler();
error_reporting(E_ALL);
// set path
$bearPath = realpath(dirname(__DIR__));
$bearDemoPath = __DIR__ . '/sites/beardemo.local';
$pandaPath = dirname(__DIR__) . '/vendors/Panda';
// set autoloder
set_include_path($bearPath . PATH_SEPARATOR . $bearDemoPath . PATH_SEPARATOR . $pandaPath . PATH_SEPARATOR . get_include_path());
spl_autoload_register('bearTestAutoloder');
BEAR::set('page', new BEAR_Page_Cli(array()));
function bearTestAutoloder($class) {
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    if(file_exists($file)) {
        include_once $file;
    }
}
