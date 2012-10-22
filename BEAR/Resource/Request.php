<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Request
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Request.php 2503 2011-06-11 10:09:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */

/**
 * リソースリクエスト
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Request
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Request.php 2503 2011-06-11 10:09:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Request extends BEAR_Base
{
    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * リソースリクエスト
     *
     * @return BEAR_Ro
     * @throws Exception Ro内部で発生した例外
     */
    public function request()
    {
        $uri = $this->_config['uri'];
        $values = $this->_config['values'];
        $options = $this->_config['options'];

        // URIのクエリーと$valuesをmerge
        $parse = parse_url($uri);
        if (!isset($parse['scheme'])) {
            $this->_mergeQuery($uri, $values);
        }
        $isNotRead = $this->_config['method'] !== BEAR_Resource::METHOD_READ;
        if (!$isNotRead) {
            $hasCsrfOption = false;
        } elseif (isset($options[BEAR_Resource::OPTION_CSRF]) && $options[BEAR_Resource::OPTION_CSRF] === true) {
            // リソースリクエストオプション
            $hasCsrfOption = true;
        } elseif ($this->_config[BEAR_Resource::OPTION_CSRF] === true) {
            // yaml
            $hasCsrfOption = true;
        } else {
            $hasCsrfOption = false;
        }
        if (!$isNotRead) {
            $hasPoeOption = false;
        } elseif (isset($options[BEAR_Resource::OPTION_POE]) && $options[BEAR_Resource::OPTION_POE] === true) {
            // リソースリクエストオプション
            $hasPoeOption = true;
        } elseif ($this->_config[BEAR_Resource::OPTION_POE] === true) {
            // yaml
            $hasPoeOption = true;
        } else {
            $hasPoeOption = false;
        }
        if ($hasCsrfOption || $hasPoeOption) {
            $formToken = BEAR::dependency('BEAR_Form_Token');
            /* @var $formToken BEAR_Form_Token */
            $isTokenCsrfValid = $hasCsrfOption ? $formToken->isTokenCsrfValid() : true;
            if ($isTokenCsrfValid !== true) {
                throw $this->_exception('CSRF');
            }
            $isTokenPoeValid = $hasPoeOption ? $formToken->isTokenPoeValid() : true;
            if ($isTokenPoeValid !== true) {
                $headers = array('request config' => $this->_config, 'msg' => 'invalid token');
                $code = BEAR::CODE_BAD_REQUEST;
                $config = compact('headers', 'code');
                $ro = BEAR::factory('BEAR_Ro', $config);
                $ro->setConfig('uri', $uri);
                $ro->setConfig('values', $values);
                $ro->setConfig('options', $options);
                return $ro;
            } else {
                $formToken->newSessionToken();
            }
        }
        $config = $this->_config;
        $config['uri'] = $uri;
        $config['values'] = $values;
        $resourceRequestCache = BEAR::factory('BEAR_Resource_Request_Cache', $config);
        try {
            $ro = $resourceRequestCache->request();
            // 中で例外が発生しなかったらPOEオプションで使ったトークンを使用済みにマークする
            // @todo staticコールを廃止
            //             BEAR_Form::finishTokens();

            /* @todo 下のifブロックを置き換える
            $isOkRo = ($ro instanceof BEAR_Ro && $ro->getCode() === BEAR::CODE_OK);
            $isNotRo = ($ro instanceof BEAR_Ro === false);
            if (!$isOkRo && $isNotRo) {
            $body = $ro;
            $ro = BEAR::factory('BEAR_Ro');
            $ro->setBody($body);
            }
             */
            if ($ro instanceof BEAR_Ro && $ro->getCode() === BEAR::CODE_OK) {
                // $options ポストプロセスクラス
            } elseif ($ro instanceof BEAR_Ro === false) {
                $body = $ro;
                $ro = BEAR::factory('BEAR_Ro');
                $ro->setBody($body);
            }
//            $request = ("{$this->_config['method']} {$uri}") .
//            ($values ? '?' . http_build_query($values) : '');
            self::_actionPostProcess($ro);
        } catch (Exception $e) {
            if (get_class($e) === 'Panda_Exception') {
                // HTTPエラー画面
                Panda::onException($e);
                throw($e);
            }

            if (BEAR::exists('page')) {
                $page = BEAR::get('page');
                if (method_exists($page, 'onException')) {
                    $page->onException($e);
                }
            }
            if ($this->_config['debug']) {
                $info = method_exists($e, 'getInfo') ? $e->getInfo() : '';
                Panda::error(get_class($e), $e->getCode() . ' ' . $e->getMessage(), $info);
            }
            //エラー (400=bad requset, or 500=server error
            $trace = $e->getTrace();
            $refTrace =& $trace;
            $trace = array_shift($refTrace);
            if (isset($trace['args'])) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $args = $trace['args'];
            } else {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $args = '';
            }
            $headers = array();
            $exception = array(
                'class' => get_class($e),
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            );
            $headers['_exception'] = $exception;
            if (method_exists($e, 'getInfo')) {
                $headers['_info'] = $e->getInfo();
            }
            $ro = BEAR::factory('BEAR_Ro');
            $ro->setHeaders($headers)->setCode($e->getCode());
        }
        if ($this->_config['debug']) {
            BEAR::dependency('BEAR_Ro_Debug', $this->_config)->debugShowResource($ro);
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
     * @return mixed
     *
     * @throws BEAR_Resource_Execute_Exception
     */
    private function _actionPostProcess(BEAR_Ro &$ro)
    {
        $body = $ro->getBody();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $Info = array();
        $info['totalItems'] = count($body);
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
            $info['page_numbers'] = array(
                'current' => $pager->pager->getCurrentPageID(),
                'total' => $pager->pager->numPages()
            );
            list($info['from'], $info['to']) = $pager->pager->getOffsetByPageId();
            $links = $pager->getLinks();
            $ro->setLink(BEAR_Resource::LINK_PAGER, $links);
            $ro->setHeaders($info);

            $info['page_numbers'] = array(
                'current' => $pager->pager->getCurrentPageID(),
                'total' => $pager->pager->numPages()
            );
            list($info['from'], $info['to']) = $pager->pager->getOffsetByPageId();
            $info['limit'] = $info['to'] - $info['from'] + 1;
            $pager->setPagerLinks($links, $info);
        }
        // コールバックオプション 1
        if (isset($options['callback'])) {
            if (is_callable($options['callback'])) {
                call_user_func($options['callback'], $body);
            } else {
                $msg = 'BEAR_Resource callback failed.';
                $info = array(
                    'callback' => $options['callback']
                );
                throw $this->_exception($msg, array('info' => $info));
            }
        }
        // コールバックオプション rec
        if (isset($options['callbackr'])) {
            if (is_callable($options['callbackr'])) {
                array_walk_recursive($body, $options['callbackr']);
            } else {
                $msg = 'BEAR_Resource callback_r failed.';
                $info = array(
                    'callbackr' => $options['callback_r']
                );
                throw $this->_exception($msg, array('info' => $info));
            }
        }
        $ro->setBody($body);
    }

    /**
     * URIについたクエリーをmergeする
     *
     * usr?id=1というURIはuriがuserでvaluesが　array('id'=>1)として扱われます。
     *
     * @param string &$uri    URI
     * @param array  &$values 引数
     *
     * @return void
     */
    private function _mergeQuery(&$uri, array &$values = array())
    {
        $newValues = null;
        $parse = parse_url($uri);
        $query = isset($parse['query']) ? $parse['query'] : '';
        parse_str($query, $parsedValues);
        if ((bool)$parsedValues === false) {
            return;
        }
        // ?の前を_uriにock
        $uri = $parse['path'];
        // URIにクエリー引数がついていて場合にはmergeする
        $values = array_merge($values, $parsedValues);
    }
}
