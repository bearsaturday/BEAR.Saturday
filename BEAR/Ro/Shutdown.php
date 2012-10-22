<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Shutdown
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * onShutdownプロトタイプリソース
 *
 * shutdown時にリクエストされるリソースのプロトタイプクラスです。
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Shutdown
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net/
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
     *
     * @return void
     */
    public function set(BEAR_Ro_Prototype $prototypeRo)
    {
        $this->_ro[] = $prototypeRo;
    }

    /**
     * Shutdown時にリクエストされるリソースのリクエスト
     *
     * @return void
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
     *
     * @return void
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
