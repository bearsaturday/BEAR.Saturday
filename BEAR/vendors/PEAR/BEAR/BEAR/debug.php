<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @link      http://api.bear-project.net/BEAR/BEAR.html
 */
require 'Panda.php';
require 'Panda/inc/FirePHPCore/FirePHP.class.php';
require 'Panda/inc/FirePHPCore/fb.php';
/**
 * パーミッションチェック
 */
$config = new PEAR_Config();
$pearDataPath = $config->get('data_dir');
if (strpos(_BEAR_BEAR_HOME, $pearDataPath)) {
    $dataDir = "$pearDataPath/BEAR";
} else {
    $dataDir = _BEAR_BEAR_HOME . '/data/';
}
if (file_exists("$pearDataPath/BEAR")) {
    $dataDir = '$pearDataPath/BEAR';
} elseif (file_exists($pearDataPath . '/data')) {
    $dataDir = 'BEAR/data';
}
$exit = false;
if (PHP_SAPI !== 'cli') {
    $isBearInfo = isset($_GET['_bearinfo']);
    $isWritable = is_writable(_BEAR_APP_HOME . '/logs') && is_writable(_BEAR_APP_HOME . '/tmp/smarty_templates_c');
    $isBearDirExists = file_exists(_BEAR_APP_HOME . '/htdocs/__bear');
    $isPandaExists = file_exists(_BEAR_APP_HOME . '/htdocs/__panda');
    if (!$isWritable) {
        $info = '<div><code>sudo chmod -R 777 ' . _BEAR_APP_HOME . '/logs;</code></div>';
        $info .= '<div><code>sudo chmod -R 777 ' . _BEAR_APP_HOME . '/tmp;</code></div>';
        $subHeading = 'このコードをシェルで実行してください';
        Panda::message('フォルダに書き込み権限を与えてください。またはtmp/smarty_templates_cフォルダがあるか確認してください', $subHeading, $info);
        $exit = true;
    }
    if (isset($_GET['_bearinfo']) || !$isWritable) {
        $ref =  new ReflectionClass('Panda');
        $pandaFile = $ref->getFileName();
        $ref =  new ReflectionClass('PEAR');
        $pearFile = $ref->getFileName();
        $heading = 'BEAR Ver. ' . BEAR::VERSION;
        $info = '<h3>BEAR Path</h3><div><code>' . _BEAR_BEAR_HOME . '</code></div>';
        $info .= '<h3>Panda Path</h3><div><code>' . $pandaFile . '</code></div>';
        $info .= '<h3>PEAR Path</h3><div><code>' . $pearFile . '</code></div>';
        $info .= '<h3>App Path</h3><div><code>' . _BEAR_APP_HOME . '</code></div>';
        $info .= '<h3>Include Path</h3><div><code>' . get_include_path() . '</code></div>';
        $info .= '<h3>Others</h3><div>';
        $info .= '<div><a href="?_cc">キャッシュクリア</a></div>';
        $info .= '<div><a href="?_beardebugsetting">開発環境のセットアップ</a></div>';
        $info .= '<div><a href="http://code.google.com/p/bear-project/wiki/manual?tm=6" target="bearmanuak">マニュアル</a></div>';
        Panda::message($heading, '', $info);
        $exit = true;
    }
    if (isset($_GET['_beardebugsetting'])) {
        $infoDir = '<h3>デバック画面の設置</h3>';
        $infoDir .= '<div><code>sudo ln -s ' . _BEAR_BEAR_HOME . '/data/htdocs/__bear ';
        $infoDir .= _BEAR_APP_HOME . '/htdocs; </code></div>';
        $infoDir .= '<div><code>sudo ln -s ' . '/tmp/new-pear/pear/php' . '/Panda/data/htdocs/__panda ';
        $infoDir .= _BEAR_APP_HOME . '/htdocs; </code></div>';
        $infoDir .= '<h3>bearコマンド(CLI)のアプリケーションホーム設定</h3>';
        $infoDir .= '<div><code>bear set-app ' . _BEAR_APP_HOME . ';</code></div>';
        $infoDir .= '<h3>オンラインエディタの設定</h3>';
        $infoDir .= '<div><code>sudo ln -s ' . _BEAR_BEAR_HOME . '/data/htdocs/__edit ';
        $infoDir .= _BEAR_APP_HOME . '/htdocs; </code></div>';
        $infoDir .= '<p>※保存するためには対象ファイルのパーミッションを変更する必要があります</p>';
        $subHeading = 'このコードをシェルで実行してください';
        Panda::message('開発環境のセットアップ', $subHeading, $infoDir);
        $exit = true;
    }
    if (isset($_GET['_error'])) {
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', 1);
        return;
    }
    // exit
    if ($exit === true) {
        exit();
    }
}

