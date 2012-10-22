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
 * @version    SVN: Release: @package_version@ $Id: Server.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * BEARリソースsocketサーバー
 *
 * <pre>
 * リソースをソケットでサービスします。
 * サーバーはデーモンモードでちあがります。
 *
 * クライアントはソケット接続した後以下のようにリソースをCRUDで操作します。
 *
 * read user/blog?id=10&blog_id=20
 * update user/blog?id=10&blog_id=20&name=new
 *
 * サーバーに接続しているクライアントに一斉に通知するのに
 * broadcastというメソッドが使用できます。一斉通知以外はreadと同じです。
 *
 * broadcast user/blog?id=10&blog_id=20
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Server
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Server.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Server extends BEAR_Base
{
    /**
     * サービススタート
     *
     * @param int    $port        ポート番号
     * @param bool   $isFork      サーバータイプ true:folk false:sequential
     * @param string $handlerName サーバーハンドラ名
     * @param bool   $ipAddress   サーバーIP
     *
     * @return void
     * @see http://pear.php.net/manual/ja/package.networking.net-server.net-server.create.php
     */
    public function start(
        $port = 103754,
        $isFork = true,
        $handlerName = 'BEAR_Resource_Server_Handler',
        $ipAddress = false
    ) {
        $type = $isFork ? 'fork' : 'sequential';
        if (!$ipAddress) {
            $hostname = exec('uname -n');
            $ipAddress = gethostbyname($hostname);
        }
        if (class_exists($handlerName)) {
            $server = &Net_Server::create($type, $ipAddress, $port);
            $handler = &new $handlerName();
            $server->setCallbackObject($handler);
            $server->_debug = $this->_config['debug'];
            $this->_printStartUpinfo("$ipAddress $port", $type);
            $server->start();
            if (PEAR::isError($server)) {
                echo $server->getMessage() . "\n";
            }
        } else {
            trigger_error('Bad Handler :' . $handlerName, E_USER_ERROR);
        }
    }

    /**
     * スタートアップメッセージ表示
     *
     * @param string $ipAddress IPアドレス
     * @param string $type      サーバータイプ
     *
     * @return void
     */
    private function _printStartUpinfo($ipAddress, $type)
    {
        echo 'BEAR Service started...' . PHP_EOL;
        echo 'App : ' . _BEAR_APP_HOME . PHP_EOL;
        echo 'BEAR: ' . BEAR::VERSION . PHP_EOL;
        echo "TYPE: {$type}" . PHP_EOL;
        echo "IP  : $ipAddress" . PHP_EOL . PHP_EOL;
    }
}