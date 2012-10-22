<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR_Dev
 * @subpackage Profile
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */

/**
 * プロファイラースタート（デバック用）
 *
 * ?_profクエリーをつけるとデバックモードoffで実行され、実行開始時にプロファイリングがスタートします。
 */
if (function_exists('xhprof_enable') && isset($bearMode) && $bearMode && isset($_GET['_prof'])
) {
    include 'BEAR/Prof.php';
    BEAR_Prof::start(1);
    $bearMode = 0;
}