<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Http.php 1244 2009-11-30 02:53:51Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * HTTPリソースクラス
 *
 * <pre>
 * HTTPリソースをリソースとして扱うクラスです。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Http.php 1244 2009-11-30 02:53:51Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 *  */
class BEAR_Resource_Execute_Http extends BEAR_Resource_Execute_Adaptor
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
     * リソースアクセス
     *
     * @return mixed
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
            $request->setHeader("user-agent", 'BEAR/' . BEAR::VERSION);
            if ($this->_config['method'] === BEAR_Resource::METHOD_CREATE || $this->_config['method'] === BEAR_Resource::METHOD_UPDATE) {
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
                $info = array('code' => $code,
                    'headers' => $headers);
                throw $this->_exception($response->getBody(), $info);
            }
        } catch(HTTP_Request2_Exception $e) {
            throw $this->_exception($e->getMessage());
        } catch(Exception $e) {
            throw $this->_exception($e->getMessage());
        }
        $rss = new XML_RSS($body, 'utf-8', 'utf-8');
        PEAR::setErrorHandling(PEAR_ERROR_RETURN);
        /**
         * @todo Panda::setPearErrorHandling(仮称）に変更しエラーを画面化しないようにする
         */
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
        $ro = BEAR::factory('BEAR_Ro');
        /* @var $ro BEAR_Ro */
        $ro->setBody($body);
        $ro->setHeaders($headers);
        PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('Panda', 'onPearError'));
        return $ro;
    }
}