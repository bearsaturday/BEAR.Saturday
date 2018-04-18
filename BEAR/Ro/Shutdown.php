<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * onShutdownプロトタイプリソース
 *
 * shutdown時にリクエストされるリソースのプロトタイプクラスです。
 *
 *
 *
 *
 *
 * @Singleton
 */
class BEAR_Ro_Shutdown extends BEAR_Base implements BEAR_Ro_Shutdown_Interface
{
    /**
     * Shutdown時にアクセスするリソース
     *
     * @var array BEAR_Ro_Prototypeのコレクション
     */
    private $_ro = array();

    /**
     * Shutdown時に実行されるリソースプロトタイプをセット
     *
     * スクリプトShutdown時に実行されるタスクまたはリソースリクエストをセットします。
     *
     * @param BEAR_Ro_Prototype $prototypeRo
     */
    public function set(BEAR_Ro_Prototype $prototypeRo)
    {
        $this->_ro[] = $prototypeRo;
    }

    /**
     * Shutdown時にリクエストされるリソースのリクエスト
     */
    public function request()
    {
        foreach ($this->_ro as $ro) {
            $ro->request();
        }
    }

    /**
     * shutdown登録される関数
     *
     * <pre>
     * このメソッドをregister_shutdown_functionしておくと
     * shutdown時にrequest()メソッドがコールされます。
     * </pre>
     */
    public static function onShutdown()
    {
        BEAR::dependency(__CLASS__)->request();
    }

    /**
     * Register shutdown function once.
     *
     * @return BEAR_Ro_Shutdown
     */
    public function register()
    {
        register_shutdown_function(array(__CLASS__, 'onShutdown'));

        return $this;
    }
}
