<?php
/**
 * ページ変数表示
 *
 * /logs/page.logに保存されたページ状態のログを表示します。
 */

//ライブモードで使用するためApp.phpより先に読み込み
//ini_set('display_errors', 0);
require_once 'vendor/autoload.php';
require_once 'BEAR/vendors/debuglib.php';
require_once 'App.php';
require_once 'BEAR/Util.php';
ini_set('unserialize_callback_func', '');
spl_autoload_unregister(array('BEAR', 'onAutoLoad'));
$pageLog = BEAR::dependency('BEAR_Log')->getPageLog($_GET);
switch ($_GET['var']) {
    case 'page' :
        $log = $pageLog['page'];
        rsort($log, SORT_NUMERIC);
        foreach ($log as $row) {
            echo "<p class=\"uri\">{$row['uri']}</p>";
            print_a($row['page']);
        }
        break;
    case 'smarty' :
        print_a($pageLog['smarty']);
        break;
    case 'var' :
        print $pageLog['var'];
        break;
    case 'ajax' :
        $path = _BEAR_APP_HOME . '/logs/ajax.log';
        $log = file_exists($path) ? unserialize(file_get_contents($path)) : false;
        if (!$log) {
            return;
        }
        rsort($log, SORT_NUMERIC);
        foreach ($log as &$row) {
            echo "<p class=\"uri\">{$row['uri']}</p>";
            print_a($row['page']);
        }
        break;
    case 'reg' :
        $reg = $pageLog['reg'];
        $keys = array_keys($reg);
        print '<h2>Keys</h2>' . print_a($keys, 'return:1') . '<h2>Values</h2>' . print_a((array)$reg, ";return:1");
        //        print($reg) ;
        break;
    case 'include' :
        echo '<h2>Include Files (' . count($pageLog['include']) . ')</h2>';
        foreach ($pageLog['include'] as $row) {
            echo "<li>$row</li>";
        }
        echo '<h2>Declared Classes (' . count($pageLog['class']) . ')</h2>';
        foreach ($pageLog['class'] as $row) {
            echo "<li>$row</li>";
        }
        break;
    default :
        print_a($pageLog);
        break;
}
