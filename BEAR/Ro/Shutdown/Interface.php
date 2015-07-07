<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Interface.php 1510 2010-04-08 17:21:24Z koriyama@users.sourceforge.jp $
 * @link      https://github.com/bearsaturday
 */

/**
 * BEAR_Ro_Shutdownインターフェイス
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Interface.php 1510 2010-04-08 17:21:24Z koriyama@users.sourceforge.jp $
 * @link      https://github.com/bearsaturday
 */
interface BEAR_Ro_Shutdown_Interface
{
    /**
     * Shutdown時に実行されるリソースプロトタイプをセット
     *
     * スクリプトShutdown時に実行されるタスクまたはリソースリクエストをセットします。
     *
     * @param BEAR_Ro_Prototype $prototypeRo
     *
     * @return void
     */
    public function set(BEAR_Ro_Prototype $prototypeRo);

    /**
     * Shutdown時にリクエストされるリソースのリクエスト
     *
     * @return void
     */
    public function request();

    /**
     * shutdown登録される関数
     *
     * このメソッドをregister_shutdown_functionしておくと
     * shutdown時にrequest()メソッドがコールされます。
     *
     * @return void
     */
    /** @noinspection PhpAbstractStaticMethodInspection */
    public static function onShutdown();
}
