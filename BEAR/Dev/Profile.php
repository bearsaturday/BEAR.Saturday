<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Profile
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
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
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */
class BEAR_Dev_Profile
{
    /**
     * xhprof original profiler
     *
     * @var int
     */
    const XHPROF = 0;

    /**
     * xh gui profiler
     *
     * @var int
     */
    const XHGUI = 1;

    /**
     * プロファイラ依存情報
     *
     * @var array
     */
    protected static $_profilerInfo = array(
        array('href' => '/__bear/prof/', 'label' => 'XHPROF'),
        array('href' => '/__bear/xhprof/xhprof_html', 'label' => 'XH GUI')
    );

    /**
     * プロファイリングスタート
     *
     * BEAR_Prof::stop()でストップを指定しないときはスクリプト終了までのプロファイルが取れます。
     *
     * @return void
     */
    protected static function start()
    {
        if (function_exists('xhprof_enable')) {
        } else {
            trigger_error('xhprof is not enabled.', E_USER_ERROR);
        }
    }

    /**
     * プロファイリングストップ
     *
     * プロファイラーリンクの表示します
     *
     * @param int $type 0:xhprof 1:xh gui
     *
     * @return void
     */
    public static function stop($type = 0)
    {
        $xhprof_data = xhprof_disable();
        $isAjax = BEAR::dependency('BEAR_Page_Ajax')->isAjaxRequest();
        if ($isAjax === true || PHP_SAPI === 'cli') {
            return;
        }
        $xhprofRuns = new XHProfRuns_Default();
        if (BEAR::exists('app')) {
            $app = BEAR::get('app');
            $appName = $app['core']['info']['id'];
        } else {
            $appName = 'bear.app';
        }
        $baseHref = self::$_profilerInfo[$type]['href'];
        $runId = $xhprofRuns->save_run($xhprof_data, $appName);
        $href = "{$baseHref}/index.php?run={$runId}&source={$appName}";
        $link = '<br><a style="padding: 3px; background-color: red; color: white; font-family:';
        $link .= 'Verdana; font-style: normal; font-variant: normal; font-weight: bold; font-size: 8pt;';
        $link .= ' " name="" target="_blank" href="' . $href . '"">' . self::$_profilerInfo[$type]['label'] . '</a>';
        echo $link;
    }
}
