<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Request.php 1304 2009-12-22 06:32:27Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * リソースアダプタークラス
 *
 * <pre>
 * リソースリクエストクラスです。DIコンテナでBEAR_Resourceクラスに注入されます。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Request.php 1304 2009-12-22 06:32:27Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 * @abstract
 */
class BEAR_Resource_Request extends BEAR_Base
{

    /**
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * リソースリクエスト
     *
     * @param string $uri    URI
     * @param array  $values 引数
     * @param string $method リクエストメソッド
     *
     * @return BEAR_Ro
     */
    public function request()
    {
        $isToken = (isset($this->_config['options'][BEAR_Resource::OPTION_TOKEN]) && $this->_config['options'][BEAR_Resource::OPTION_TOKEN]);
        $isPoe = (isset($this->_config['options'][BEAR_Resource::OPTION_POE]) && $this->_config['options'][BEAR_Resource::OPTION_POE]);
        $isNotRead = $this->_config['method'] !== BEAR_Resource::METHOD_READ;
        $doCheckToken = $isNotRead && ($isToken || $isPoe);
        if ($doCheckToken) {
            $isTokenValid = $this->_isTokenValid($isPoe);
        } else {
            $isTokenValid = true;
        }
        if ($isTokenValid === false) {
            $headers = array('request config' => $this->_config,
                'msg' => 'invalid token');
            $code = BEAR::CODE_BAD_REQUEST;
            $config = compact('headers', 'code');
            $ro = BEAR::factory('BEAR_Ro', $config);
            return $ro;
        }
        $resourceRequestCache = BEAR::factory('BEAR_Resource_Request_Cache', $this->_config);
        try {
            $ro = $resourceRequestCache->request();
            // 中で例外が発生しなかったらPOEオプションで使ったトークンを使用済みにマークする
            BEAR_Form::finishTokens();
            if ($ro instanceof BEAR_Ro && $ro->getCode() === BEAR::CODE_OK) {
                // $options ポストプロセスクラス
            } elseif ($ro instanceof BEAR_Ro === false) {
                $body = $ro;
                $ro = BEAR::factory('BEAR_Ro');
                $ro->setBody($body);
            }
            $ro->setHeader('request', array(
                'uri' => $this->_config['uri']));
            self::_actionPostProcess($ro);
        } catch(Exception $e) {
            if (get_class($e) === 'Panda_Exception') {
                // HTTPエラー画面
                Panda::onException($e);
                throw($e);
            }
            $page = BEAR::get('page');
            if (method_exists($page, 'onException')) {
                $page->onException($e);
            }
            if ($this->_config['debug']) {
                Panda::onException($e, false);
            }
            //エラー (400=bad requset, or 500=server error
            $trace = array_shift($e->getTrace());
            if (isset($trace['args'])) {
                $args = $trace['args'];
            } else {
                $args = '';
            }
            $headers = array();
            $headers['_exception'] = get_class($e);
            $headers['_msg'] = $e->getMessage();
            $headers['_bear_request'] = $args;
            $ro = BEAR::factory('BEAR_Ro');
            $ro->setHeaders($headers);
            $ro->setCode($e->getCode());
        }
        return $ro;
    }

    /**
     * リソース後処理
     *
     * <pre>
     * リソース取得の結果に対しての後処理を行います。
     * 後処理にはコールバック関数の適用や、DBページャーの
     * 結果取り出しなどがあります。
     * </pre>
     *
     * @param BEAR_Ro &$ro BEAR_Roオブジェクト
     *
     * @return array
     */
    private function _actionPostProcess(BEAR_Ro &$ro)
    {
        $body = $ro->getBody();
        $options = $this->_config['options'];
        // ページャーリザルト処理
        if (PEAR::isError($body) || !$body) {
            return;
        }
        // ページャーオプション
        if (isset($options[BEAR_Resource::OPTION_PAGER])) {
            $pager = BEAR::factory('BEAR_Pager');
            $pagerOptions['perPage'] = $options[BEAR_Resource::OPTION_PAGER];
            $pager->setOptions($pagerOptions);
            $pager->makePager($body);
            $body = $pager->getResult();
            $info['page_numbers'] = array('current' => $pager->pager->getCurrentPageID(),
            'total' => $pager->pager->numPages());
            list($info['from'], $info['to']) = $pager->pager->getOffsetByPageId();
            $ro->setLink(BEAR_Resource::LINK_PAGER, $pager->getLinks());
            $ro->setHeaders($info);
        }
        // コールバックオプション 1
        if (isset($options['callback'])) {
            if (is_callable($options['callback'])) {
                call_user_func($options['callback'], $body);
            } else {
                $msg = 'BEAR_Resource callback failed.';
                $info = array(
                    'callback' => $options['callback']);
                throw $this->_exception($msg, array(
                    'info' => $info));
            }
        }
        // コールバックオプション rec
        if (isset($options['callbackr'])) {
            if (is_callable($options['callbackr'])) {
                array_walk_recursive($body, $options['callbackr']);
            } else {
                $msg = 'BEAR_Resource callback_r failed.';
                $info = array(
                    'callbackr' => $options['callback_r']);
                throw $this->_exception($msg, array(
                    'info' => $info));
            }
        }
        //テンプレート
        if (isset($options['template'])) {
            $templatePath = 'elements/' . $options['template'] . '.tpl';
            $fileExists = !file_exists(_BEAR_APP_HOME . '/App/views/' . $templatePath);
            if ($this->_config['debug'] && $fileExists) {
                $filePath = _BEAR_APP_HOME . '/App/views/' . $templatePath;
                $msg = 'No Element Template';
                $info = array('template' => $filePath);
                throw $this->_exception($msg, array(
                    'info' => $info));
            }
            $smarty = BEAR::dependency('BEAR_Smarty');
            $smarty->assign($options['template'], $body);
            $body = $smarty->fetch($templatePath);
        }
        $ro->setBody($body);
    }

    /**
     * トークン検査
     *
     * <pre>
     * トークンが有効か無効を調べます。$options['poe']がtrueの場合は
     * POE(Post Once Exactly)検査をし、そのリソースアクセスが一度しか
     * 行われないようにチェックします。二重送信を防止する仕組みです
     * </pre>
     *
     * @param bool $isPoe POEチェックも行うか
     *
     * @return bool true=valid
     *
     */
    private function _isTokenValid($isPoe)
    {
        $token = isset($_POST['_token']) ? $_POST['_token'] : (isset($_GET['_token']) ? $_GET['_token'] : null);
        $poeLog = array();
        if ($isPoe) {
            if (isset($_SESSION['_used_token']) && is_array($_SESSION['_used_token'])) {
                $isPoe = !key_exists($token, $_SESSION['_used_token']);
            } else {
                $isPoe = true;
            }
            $poeLog['is unused token'] = $isPoe;
        }
        $stoken = BEAR_Form::getSessionToken();
        $isGenuine = (substr($token, 1, 4) == $stoken);
        $isTokenValid = (($isGenuine !== false) && ($isPoe !== false));
        if ($isTokenValid) {
            $result = true;
            $msg = "OK";
            //session
            BEAR_Form::registerUsedToken($token);
        } else {
            $msg = " NG";
            $result = false;
        }
        $log = array('resource URI' => $this->_config['uri'],
            'submit token' => $token,
            'session token' => $stoken,
            'is genuine' => $isGenuine) + $poeLog;
        $this->_log->log('Token Validation' . $msg, $log);
        return $result;
    }
}