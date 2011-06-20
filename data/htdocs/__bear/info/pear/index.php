<?php
require_once 'App.php';
include_once (_BEAR_APP_HOME . '/App/data/dev.config.php');

if (!$isSet) {
    Panda::error('現在利用できません。', 'dev.config.phpファイルを編集して設定を完了させてください', _BEAR_APP_HOME . '/App/data/dev.config.php');
    exit();
}

/**
 * Put this file in a web-accessible directory as index.php (or similar)
 * and point your webbrowser to it.
 */

// OPTIONAL: If you have protected this webfrontend with a password in a
// custom way, then uncomment to disable the 'not protected' warning:
$pear_frontweb_protected = true;

/***********************************************************
 * Following code tests $pear_dir and loads the webfrontend:
 */
if (!file_exists($pear_dir . '/PEAR.php')) {
    trigger_error('No PEAR.php in supplied PEAR directory: (PEARディレクトリにPEAR.phpがありません）' . $pear_dir, E_USER_ERROR);
}
ini_set('include_path', $pear_dir);
require_once ('PEAR.php');

// Include WebInstaller
putenv('PHP_PEAR_INSTALL_DIR=' . $pear_dir); // needed if unexisting config
require_once ('pearfrontendweb.php');
?>
