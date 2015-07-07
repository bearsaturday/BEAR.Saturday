<?php
/**
 * プロファイラスタートスクリプト
 *
 * XH GUIを使ったプロファイリングです。
 * xdebug, xhprof, graphviz（コールグラフ描画）,XH GUIのセットアップが必要です。
 *
 * @category   BEAR
 * @package    BEAR_Dev
 * @subpackage Profile
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 * @link       http://@link_url@
 */
if (function_exists('xhprof_enable')) {
    include_once "BEAR/vendors/xhprof/xhprof_lib/config.php";
    include_once "BEAR/vendors/xhprof/xhprof_lib/utils/xhprof_lib.php";
    include_once "BEAR/vendors/xhprof/xhprof_lib/utils/xhprof_runs.php";
    include_once 'BEAR/Dev/Profile.php';
    /** @noinspection PhpUndefinedConstantInspection */
    /** @noinspection PhpUndefinedConstantInspection */
    xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
    //xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
    register_shutdown_function(array('BEAR_Dev_Profile', 'stop'), BEAR_Dev_Profile::XHGUI);
    $bearMode = 0;
}
