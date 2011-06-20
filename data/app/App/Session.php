<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Session
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id: Session.php 2577 2011-06-20 08:57:15Z koriyama@bear-project.net $
 * @link       http://@link_url@
 */

/**
 * Session
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Session
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id: Session.php 2577 2011-06-20 08:57:15Z koriyama@bear-project.net $
 * @link       http://@link_url@
 */
class App_Session extends BEAR_Base
{

    /**
     * セッション延長
     *
     * @var string
     */
    const EXTEND = 'extend';

    /**
     * ログアウト
     *
     * @var string
     */
    const LOGOUT = 'logout';

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $this->_header = BEAR::dependency('BEAR_Page_Header');
        $this->_session = BEAR::dependency('BEAR_Session');
        switch (true) {
            case (isset($_GET['extend'])):
                // セッション延長
                $this->_mode = self::EXTEND;
                break;
            case (isset($_GET['logout'])):
                // セッション破棄
                $this->_mode = self::LOGOUT;
                break;
            default:
                // どうしますか画面
                $this->_mode = null;
        }
    }

    /**
     * Session timeout
     *
     * @return void
     * @throws App_Session_Exception セッションタイムアウト例外
     */
    public function onSessionTimeOut()
    {
        switch (true) {
            case ($this->_mode === self::EXTEND):
                $this->_session->updateIdle();
                $uri = $this->_session->get('url');
                $this->_header->redirect($uri);
                break;
            case ($this->_mode === self::LOGOUT):
                $this->_session->destroy();
                $this->_header->redirect('.');
                break;
            default:
                //セッションタイムアウト例外(App_Page::onExceptuon()でキャッチ)
                throw $this->_exception('Session Timeout');
        }
    }
}