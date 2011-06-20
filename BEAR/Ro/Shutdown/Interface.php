<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Interface.php 1510 2010-04-08 17:21:24Z koriyama@users.sourceforge.jp $
 * @link      http://www.bear-project.net/
 */

/**
 * BEAR_Ro_Shutdownインターフェイス
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Interface.php 1510 2010-04-08 17:21:24Z koriyama@users.sourceforge.jp $
 * @link      http://www.bear-project.net/
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
     * <pre>
     * このメソッドをregister_shutdown_functionしておくと
     * shutdown時にrequest()メソッドがコールされます。
     * </pre>
     *
     * @return void
     */
    public static function onShutdown();
}