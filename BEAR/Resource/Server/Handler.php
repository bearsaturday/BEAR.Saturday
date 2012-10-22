<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Server
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Handler.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * BEARリソースsocketサーバーハンドラー
 *
 * <pre>
 * リモートクライアントからソケット接続でリソースを行うハンドラです。
 * 以下のように通信を行います。
 *
 * クライアント書式 :
 *   read uri&param1=val1&param2&val2
 *
 * レスポンス :
 *   200 (コード)
 *   アトリビュート（単数、複数）
 *   (空行)
 *   データ
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Server
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Handler.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Server_Handler extends Net_Server_Handler
{

    /**
     * Constructor
     */
    public function __construct()
    {
        echo "start";
    }

    /**
     * 接続
     *
     * @param int $clientId クライアント番号
     *
     * @return void
     */
    public function onConnect($clientId = 0)
    {
        echo "$clientId is connecetd." . PHP_EOL;
    }

    /**
     * データ受信ハンドラ
     *
     * @param int    $clientId クライアントID
     * @param string $data     受信データ
     *
     * @return void
     */
    public function onReceiveData($clientId = 0, $data = "")
    {
        $data = trim($data);
        $parsed = explode(' ', $data);
        $method = strtolower($parsed[0]);
        $uri = $parsed[1];
        switch ($method) {
        case '' :
            break;
        case '/info' :
            $info = $this->_server->getClientInfo();
            $this->_server->sendData($clientId, var_export($info, true));
            exit();
        case '/close' :
            $this->_server->closeConnection($clientId);
            exit();
        case '/help' :
            $help = 'Usage: <method> <url>' . PHP_EOL;
            $this->_server->sendData($clientId, $help);
            exit();
        default :
        }
        $resource = BEAR::dependency('BEAR_Resource');
        $params = array('uri' => $uri);
        switch ($method) {
        case 'create' :
        case 'post' :
            $resource->create($params);
            break;
        case 'read' :
        case 'broadcast' :
        case 'get' :
            $resource->read($params);
            break;
        case 'update' :
        case 'put' :
            $resource->update($params);
            break;
        case 'delete' :
            $resource->delete($params);
            break;
        default :
            $resource = false;
            break;
        }
        if ($resource !== false) {
            $ro = $resource->getRo();
            $this->sendData($clientId, $method, $ro);
        } else {
            // BAD Request
            $data = BEAR::CODE_BAD_REQUEST;
            $data .= PHP_EOL . PHP_EOL;
            $this->_server->sendData($clientId, $data);
        }
    }

    /**
     * データ送信
     *
     * @param int     $clientId クライアントID
     * @param string  $method   リクエストメソッド名
     * @param BEAR_Ro $ro       RO
     *
     * @return void
     */
    public function sendData($clientId,
        /** @noinspection PhpUnusedParameterInspection */
        $method, BEAR_Ro $ro)
    {
        $code = $ro->getCode();
        $hearders = $ro->getHeaders();
        if (isset($hearders['broadcast'])) {
            $this->_server->broadcastData($code . PHP_EOL, array($clientId));
            $this->_server->broadcastData('Content-Type: text/php' . PHP_EOL, array($clientId));
            $type = 'X-Socket-Type: broadcast' . PHP_EOL;
            $this->_server->broadcastData($type, array($clientId));
            $this->_server->broadcastData(PHP_EOL, array($clientId));
            $data = serialize($hearders['broadcast']) . PHP_EOL;
            $this->_server->broadcastData($data, array($clientId));
        }
        $this->_server->sendData($clientId, $code . PHP_EOL);
        $this->_server->sendData($clientId, 'Content-Type: text/php' . PHP_EOL);
        $this->_server->sendData($clientId, PHP_EOL);
        $data = $ro->getBody();
        $this->_server->sendData($clientId, serialize($data) . PHP_EOL);
    }
}
