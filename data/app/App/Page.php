<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Page
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */

/**
 * Page
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Page
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 * @abstract
 */
abstract class App_Page extends BEAR_Page
{

    /**
     *  セッション
     *
     * @var BEAR_Session
     */
    protected $_session;

    /**
     * リソースアクセス
     *
     * @var BEAR_Resource
     */
    protected $_resource;

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        parent::onInject();
        //$this->_session = BEAR::dependency('BEAR_Session');
        $this->_resource = BEAR::dependency('BEAR_Resource');
    }

    /**
     * 出力
     *
     * @return void
     */
    public function onOutput()
    {
        $this->display();
    }

    /**
     * 例外
     *
     * @return void
     * @throws $e 受け取った例外
     */
    public function onException(Exception $e)
    {
        try {
            throw $e;
        } catch (App_Session_Exception $e) {
            // セッションタイムアウト
//            $this->display('/session/timeout.tpl');
//            $this->end();
        }
    }

    /**
     * セッションタイムアウト
     *
     * @return void
     */
    public function onSessionTimeout()
    {
    }
}
