<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR
 * @subpackage Debug
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @link       http://www.bear-project.net/
 */

require_once 'Panda.php';
require_once 'FirePHPCore/FirePHP.class.php';
require_once 'FirePHPCore/fb.php';

// シャットダウン関数登録
if (isset($_SERVER) && isset($_SERVER['REQUEST_URI'])
    && substr($_SERVER['REQUEST_URI'], 1, 2) !== '__') {
        register_shutdown_function(array('BEAR_Dev_Util', 'onShutdownDebug'));
        register_shutdown_function(array('BEAR_Log', 'onShutdownDebug'));
}

// エラー初期化(Panda)
if (defined('_BEAR_APP_HOME')) {
    $validPath = array(_BEAR_APP_HOME . '/htdocs', _BEAR_APP_HOME . '/App');
} else {
    $validPath = array();
}
// BEAR developperのみBEAR内のエラー表示
if (isset($_SERVER['beardev']) && $_SERVER['beardev']) {
    $validPath[] = _BEAR_BEAR_HOME;
}
$pandaConfig = array(Panda::CONFIG_DEBUG => $appConfig['core']['debug'],  // デバックモード
    Panda::CONFIG_VALID_PATH => $validPath,  // エラーレポートするファイルパス
    Panda::CONFIG_LOG_PATH => _BEAR_APP_HOME . '/logs/' // fatalエラーログを保存するパス
);
if (isset($appConfig['Panda'])) {
    $pandaConfig = array_merge($pandaConfig, $appConfig['Panda']);
}
Panda::init($pandaConfig);
// デバック用画面
include _BEAR_BEAR_HOME . '/BEAR/BEAR/script/dev_info_screen.php';

// _preクエリー
if (isset($_GET['_pre'])) {
    echo '<pre>';
}

// _errorクエリー
if (isset($_GET['_error'])) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    restore_error_handler();
    restore_exception_handler();
    return;
}
// exit
if ($exit === true) {
    exit();
}

// デバック用キャッシュクリア
if (isset($_GET['_cc'])) {
    BEAR_Util::clearAllCache(true);
	exit();
}

// log
$log = array();
$log['BEAR'] = BEAR::VERSION;
if (isset($_SERVER['REQUEST_URI'])) {
    $log['URI'] = $_SERVER['REQUEST_URI'];
}
$log['time'] = _BEAR_DATETIME;
BEAR::dependency('BEAR_Log')->log('start', $log);
