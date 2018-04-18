<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * HTTPリソース
 */
class BEAR_Resource_Execute_Http extends BEAR_Resource_Execute_Adapter
{
    /**
     * リソースリクエスト実行
     *
     * リモートURLにアクセスしてRSSだったら配列に、
     * そうでなかったらHTTP Body文字列をリソースとして扱います。
     *
     * @throws BEAR_Resource_Execute_Exception
     *
     * @return BEAR_Ro
     */
    public function request()
    {
        $reqMethod = array();
        $reqMethod[BEAR_Resource::METHOD_CREATE] = HTTP_Request2::METHOD_POST;
        $reqMethod[BEAR_Resource::METHOD_READ] = HTTP_Request2::METHOD_GET;
        $reqMethod[BEAR_Resource::METHOD_UPDATE] = HTTP_Request2::METHOD_PUT;
        $reqMethod[BEAR_Resource::METHOD_DELETE] = HTTP_Request2::METHOD_DELETE;
        assert(isset($reqMethod[$this->_config['method']]));
        try {
            // 引数以降省略可能　 config で proxy とかも設定可能
            $request = new HTTP_Request2($this->_config['uri'], $reqMethod[$this->_config['method']]);
            $request->setHeader('user-agent', 'BEAR/' . BEAR::VERSION);
            $request->setConfig('follow_redirects', true);
            if ($this->_config['method'] === BEAR_Resource::METHOD_CREATE || $this->_config['method'] === BEAR_Resource::METHOD_UPDATE
            ) {
                foreach ($this->_config['values'] as $key => $value) {
                    $request->addPostParameter($key, $value);
                }
            }
            $response = $request->send();
            $code = $response->getStatus();
            $headers = $response->getHeader();
            if ($code == 200) {
                $body = $response->getBody();
            } else {
                $info = array(
                    'code' => $code,
                    'headers' => $headers
                );
                throw $this->_exception($response->getBody(), $info);
            }
        } catch (HTTP_Request2_Exception $e) {
            throw $this->_exception($e->getMessage());
        } catch (Exception $e) {
            throw $this->_exception($e->getMessage());
        }
        $rss = new XML_RSS($body, 'utf-8', 'utf-8');
        PEAR::setErrorHandling(PEAR_ERROR_RETURN);
        $rss->parse();
        $items = $rss->getItems();
        if (is_array($items) && count($items) > 0) {
            $body = $items;
            $headers = $rss->getChannelInfo();
            $headers['type'] = 'rss';
        } else {
            $headers['type'] = 'string';
            $body = array($body);
        }
        // UTF-8に
        mb_convert_variables('UTF-8', 'auto', $body);
        /* @var $ro BEAR_Ro */
        /** @noinspection PhpUndefinedMethodInspection */
        $ro = BEAR::factory('BEAR_Ro')->setBody($body)->setHeaders($headers);
        PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('Panda', 'onPearError'));

        return $ro;
    }
}
