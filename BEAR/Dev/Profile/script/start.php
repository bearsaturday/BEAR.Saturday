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
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net/
 * @link       http://@link_url@
 */
if (function_exists('xhprof_enable')) {
    include_once 'BEAR/vendors/xhprof_lib/utils/xhprof_lib.php';
    include_once 'BEAR/vendors/xhprof_lib/utils/xhprof_runs.php';
    include_once 'BEAR/Dev/Profile.php';
    xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
    //xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
    register_shutdown_function(array('BEAR_Dev_Profile', 'stop'), BEAR_Dev_Profile::XHPROF);
    $bearMode = 0;
}