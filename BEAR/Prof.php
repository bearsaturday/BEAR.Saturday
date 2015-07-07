<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Profile
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$$
 * @link      https://github.com/bearsaturday
 */

/**
 * プロファイラークラス
 *
 * xdebug + xhprofでプロファイリングを行います。
 *
 * @category  BEAR
 * @package   BEAR_Profile
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$$
 * @link      https://github.com/bearsaturday
 */
class BEAR_Prof
{
    /**
     * プロファイリングスタート
     *
     * BEAR_Prof::stop()でストップを指定しないときはスクリプト終了までのプロファイルが取れます。
     *
     * @return void
     */
    public static function start()
    {
        if (function_exists('xhprof_enable')) {
            register_shutdown_function(array(__CLASS__, 'stop'));
            xhprof_enable();
            //xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
        } else {
            trigger_error('xhprof is not enabled.', E_USER_ERROR);
        }
    }

    /**
     * プロファイリングストップ
     *
     * プロファイラーリンクの表示します
     *
     * @return void
     */
    public static function stop()
    {
        static $done = false;

        if ($done !== false) {
            return;
        }
        $done = true;
        /** @noinspection PhpUndefinedFunctionInspection */
        $xhprofData = xhprof_disable();
        $app = BEAR::get('app');
        $appName = $app['core']['info']['id']; // アプリ名とか識別する名前
        include_once _BEAR_BEAR_HOME . '/BEAR/vendors/xhprof_lib/utils/xhprof_lib.php';
        include_once _BEAR_BEAR_HOME . '/BEAR/vendors/xhprof_lib/utils/xhprof_runs.php';
        $xhprofRuns = new XHProfRuns_Default();
        $runId = $xhprofRuns->save_run($xhprofData, $appName);
        $href = "/__bear/prof/index.php?run={$runId}&source={$appName}";
        echo '<a style="padding: 3px; background-color: red; color: white; font-family: Verdana; font-style: normal; font-variant: normal; font-weight: bold; font-size: 8pt; " name="" target="_blank"' . $appName . '" href="' . $href . '"">PROFILE</a>';
    }
}
