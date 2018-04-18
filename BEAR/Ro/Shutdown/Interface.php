<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 */

/**
 * BEAR_Ro_Shutdownインターフェイス
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
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
