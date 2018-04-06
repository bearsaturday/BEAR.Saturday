<?php

require_once 'vendor/autoload.php';
//ライブモードで使用するためApp.phpより先に読み込み
require_once 'BEAR/vendors/debuglib.php';
require_once 'App.php';
require_once 'CodeSniff.php';

switch ($_GET['page']) {
    case 'home' :
        print "ファイル名を選ぶとコーディング規則にしたがっているかチェックができます。<br>";
        break;
    case 'app' :
        $path = 'App';
        break;
    case 'htdocs' :
        $path = 'htdocs';
        break;
    case 'resource' :
        $path = 'App' . DIRECTORY_SEPARATOR . 'resources';
        break;
    case 'bear' :
        BEAR_Dev_CodeSniff::showList(_BEAR_BEAR_HOME, 'bear');
        exit();
    default :
        print "err={$_GET['var']}";
        return;
}
BEAR_Dev_CodeSniff::showList(_BEAR_APP_HOME . DIRECTORY_SEPARATOR . $path);

