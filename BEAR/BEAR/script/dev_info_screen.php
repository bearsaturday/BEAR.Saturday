<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
if (isset($_GET['_pear_dir'])) {
    // パーミッションチェック
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
} else {
    $pearDataPath = '<a href="?_beardebug_setting&_pear_dir">{pear data_dir}</a>';
}
$exit = false;
if (PHP_SAPI !== 'cli') {
    $isBearInfo = isset($_GET['_bearinfo']);
    $isWritable = is_writable(_BEAR_APP_HOME . '/logs') && is_writable(_BEAR_APP_HOME . '/tmp/smarty_templates_c');
    $isBearDirExists = file_exists(_BEAR_APP_HOME . '/htdocs/__bear');
    $isPandaExists = file_exists(_BEAR_APP_HOME . '/htdocs/__panda');
    if (! $isWritable) {
        $info = '<div><code>sudo chmod -R 777 ' . _BEAR_APP_HOME . '/logs;</code></div>';
        $info .= '<div><code>sudo chmod -R 777 ' . _BEAR_APP_HOME . '/tmp;</code></div>';
        $subHeading = 'このコードをシェルで実行してください';
        Panda::message('フォルダに書き込み権限を与えてください。またはtmp/smarty_templates_cフォルダがあるか確認してください', $subHeading, $info);
        $exit = true;
    }
    if (isset($_GET['_bearinfo']) || ! $isWritable) {
        $ref = new ReflectionClass('Panda');
        $pandaFile = $ref->getFileName();
        $ref = new ReflectionClass('PEAR');
        $pearFile = $ref->getFileName();
        $heading = 'BEAR Ver. ' . BEAR::VERSION;
        $info = '<h3>BEAR Path</h3><div><code>' . _BEAR_BEAR_HOME . '</code></div>';
        $info .= '<h3>Panda Path</h3><div><code>' . $pandaFile . '</code></div>';
        $info .= '<h3>PEAR Path</h3><div><code>' . $pearFile . '</code></div>';
        $info .= '<h3>App Path</h3><div><code>' . _BEAR_APP_HOME . '</code></div>';
        $info .= '<h3>Include Path</h3><div><code>' . get_include_path() . '</code></div>';
        $info .= '<h3>Others</h3><div><ul>';
        $info .= '<li><a href="?_cc">キャッシュクリア</a></li>';
        $info .= '<li><a href="?_beardebug_setting">開発環境のセットアップ</a></li>';
        $info .= '<li><a href="?_beardebug_query">デバック用クエリー</a></li>';
        $info .= '<li><a href="http://code.google.com/p/bear-project/wiki/manual?tm=6" target="bearmanual">';
        $info .= 'BEARマニュアル</a></li>';
        $info .= '<li><a href="/__bear/bearshell/" target="bearshell">BEARシェル</a></li></ul>';
        Panda::message($heading, '', $info);
        $exit = true;
    }
    if (isset($_GET['_beardebug_setting'])) {
        $infoDir = '<h3>デバック画面の設置</h3>';
        $infoDir .= '<div><code>sudo ln -s ' . _BEAR_BEAR_HOME . '/data/htdocs/__bear ';
        $infoDir .= _BEAR_APP_HOME . '/htdocs; </code></div>';
        $infoDir .= '<div><code>sudo ln -s ' . $pearDataPath . '/Panda/data/htdocs/__panda ';
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
    } elseif (isset($_GET['_beardebug_query'])) {
        $subHeading = 'デバックモードでクエリーによるデバッグコマンドが使えます';
        $infoDir = '<h3>キャッシュクリア</h3>';
        $infoDir .= '<div><p>?_cc</p></div>';
        $infoDir .= '<h3>リソースデバック</h3>';
        $infoDir .= '<div><p>?_resource</p></div>';
        $infoDir .= '<h3>全てのエラー表示</h3>';
        $infoDir .= '<div><p>?_error</p></div>';
        $infoDir .= '<h3>改行を表示するためのpreタグ表示</h3>';
        $infoDir .= '<div><p>?_pre</p></div>';
        $infoDir .= '<h3>firePHPログ</h3>';
        $infoDir .= '<div><p>?_firelog</p></div>';
        $infoDir .= '<div>スクリプト最後に発生したエラーメッセージを表示します。</div>';
        $infoDir .= '<h3>Pandaエラーハンドラーoff</h3>';
        $infoDir .= '<div><p>?_nopanda</p></div>';
        $infoDir .= '<div> ※エラーメッセージが表示されないときなど</div>';
        $infoDir .= '<h3>プロファイリング</h3>';
        $infoDir .= '<div><p>?_prof</p></div><div> ※xdebug, xhprofの機能拡張とApp.phpでApp/prof.phpの読み込みが必要です</div>';
        Panda::message('デバック用クエリー', $subHeading, $infoDir);
        $exit = true;
    }
}
