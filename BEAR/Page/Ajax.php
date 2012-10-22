<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Page
 * @subpackage Ajax
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Ajax.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */

/**
 * BEAR_Page_Ajax
 *
 * @category   BEAR
 * @package    BEAR_Page
 * @subpackage Ajax
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Ajax.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 *
 * @Singleton
 *
 * @config bool security_check セッションを用いたダブルサブミットクッキーによりセキュリティチェック true
 */
class BEAR_Page_Ajax extends BEAR_Base
{
    /**
     * @var BEAR_Log
     */
    protected $_log;

    /**
     * @var BEAR_Session
     */
    protected $_session;
    /**
     * Ajaxコマンド
     */
    private $_ajax = array();

    /**
     * ヘッダーオブジェクト
     *
     * @var BEAR_Page_Header
     */
    private $_header;

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $this->_header = BEAR::dependency('BEAR_Page_Header');
        // CSLF対策のDouble Submit Cookieチェック
        if ($this->_config['security_check'] === true) {
            $this->checkSecurity();
        }
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * セキュリティチェック
     *
     * AJAXリクエストが不正なものでないかチェックします。
     *
     * @throws BEAR_Exception
     * @return void
     */
    public function checkSecurity()
    {
        if (!$this->isAjaxRequest()) {
            return;
        }
        $this->_session = BEAR::dependency('BEAR_Session');
        $this->_session->start();
        // AJAXセキュリティチェック
        $isOk = $this->_isValidAjaxDoubleSubmitionCookie();
        if ($isOk === false) {
            $info = array(
                'verify' => $this->_header->getRequestHeader('X_BEAR_AJAX_ARGS'),
                'session_id' => session_id(),
                '$_SERVER' => $_SERVER
            );
            throw $this->_exception('Ajax Validation NG or This is not AJAX', compact('info'));
        }
    }

    /**
     * AJAXリクエストかどうかを返す
     *
     * prototype.js jQuery他で動作します
     *
     * @return boolean
     */
    public function isAjaxRequest()
    {
        return ($this->_header->getRequestHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }

    /**
     * bear.jsからのAJAXリクエストのクライアント値を受け取る
     *
     * <pre>
     * クライアントのフォームとBear.Valueの値をonInit($args)の$argsにします。
     *
     * フォーマット:
     * array('form' =>array('フォーム名1' => 'フォーム1データ', 'フォーム名2' => 'フォーム1データ',
     *   'value' => 'Bear.Value');
     * </pre>
     *
     * <code>
     * <a href="/" rel="ajax[loading1]">ajax request</a>
     * </code>
     *
     * @return array
     */
    public function getAjaxRequest()
    {
        if ($this->isAjaxRequest()) {
            $result = $form = $arr = array();
            $form = $_POST['_form'];
            parse_str($form, $form);
            $formArr = array();
            foreach ($form as $key => &$value) {
                parse_str($value, $arr);
                $formArr[$key] = $arr;
            }
            $result['form'] = $formArr;
            $this->_log->log('AJAX Req', $result);
        } else {
            $result = array();
        }
        return $result;
    }

    /**
     * AJAXコマンドを追加
     *
     *
     * <code>
     *  // リソースをアサイン
     *  $this->addAjax('resource', array('div_person1' => 'person'), array('effect' => 'slideup'));
     *  // 生のデータをアサイン
     *  $this->addAjax('html', array('msg' => '使用できます!'), array('effect' => 'splash'));
     *  // フォームの値を変更
     *  $this->addAjax('form', array('post' => '123', 'post2' => '4567'));
     *  // JSをコール
     *  $this->addAjax('js', array('callback1' => $_SERVER, 'callback2' => $_COOKIE));
     * // 出力
     *  $this->output('ajax');
     * </code>
     *
     * @param string $ajaxCommand AJAXコマンド 'html' | 'resource' |'form' | 'js'
     * @param array  $data        AJAXコマンド引数
     * @param array  $options     AJAXコマンドオプション
     *
     * @return void
     */
    public function addAjax($ajaxCommand, array $data, array $options = array())
    {
        switch ($ajaxCommand) {
        case 'resource' :
        case 'init' :
            foreach ($data as $div => $initValueKey) {
                $page = BEAR::get('page');
                /** @var $init @page BEAR_Page */
                $init = $page->get();
                $ajaxDivBody[$div] = $init[$initValueKey];
            }
            $htmlData = array('body' => $ajaxDivBody,
            'options' => $options);
            $this->_ajax['html'][] = $htmlData;
            break;
        case 'html' :
            $htmlData = array('body' => $data, 'options' => $options);
            $this->_ajax['html'][] = $htmlData;
            break;
        default :
            $this->_ajax[$ajaxCommand][] = $data;
            break;
        }
    }

    /**
     * AJAXコマンドを取得
     *
     * @return array
     * @ignore
     */
    public function getAjaxValues()
    {
        return $this->_ajax;
    }

    /**
     * ダブルサブミッションクッキーのチェック
     *
     * bear.jsがヘッダーに付加したセッションIDがセッションIDとして適当か判断しています。
     *
     * @return bool
     */
    private function _isValidAjaxDoubleSubmitionCookie()
    {
        $sessionVerify = $this->_header->getRequestHeader('X_BEAR_SESSION_VERIFY');
        $isValid = ($sessionVerify === session_id());
        return $isValid;
    }
}
