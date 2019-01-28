<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * BEAR_Ro_Shutdownインターフェイス
 */
interface BEAR_Ro_Shutdown_Interface
{
    /**
     * Shutdown時に実行されるリソースプロトタイプをセット
     *
     * スクリプトShutdown時に実行されるタスクまたはリソースリクエストをセットします。
     */
    public function set(BEAR_Ro_Prototype $prototypeRo);

    /**
     * Shutdown時にリクエストされるリソースのリクエスト
     */
    public function request();

    /**
     * shutdown登録される関数
     *
     * このメソッドをregister_shutdown_functionしておくと
     * shutdown時にrequest()メソッドがコールされます。
     */

    /** @noinspection PhpAbstractStaticMethodInspection */
    public static function onShutdown();
}
