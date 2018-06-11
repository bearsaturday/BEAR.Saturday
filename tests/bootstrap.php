<?php

// set path
$bearPath = realpath(dirname(__DIR__));
spl_autoload_register('bearTestAutolodaer');
function bearTestAutolodaer($class) {
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    include_once $file;
}
BEAR::set('page', new BEAR_Page_Cli(array()));
BEAR::set('BEAR_Session', BEAR::factory('BEAR_Session', array('adapter' => 0, 'prefix' => '')));
