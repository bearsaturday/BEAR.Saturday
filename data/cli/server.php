<?php
error_reporting(0);

/**
 * BEARリソースソケットサーバー
 *
 * <pre>
 * リソースをソケットで使用するサーバーです。
 * <アプリケーション>/cli/server.phpなどにコピーして使います。
 * </pre>
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Server
 * @author     $Author: akihito.koriyama@gmail.com $ <anonymous@example.com>
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 */

require_once '../App.php';
// BEARサーバースタート
$server = new BEAR_Resource_Server(array());
$port = 13754;
$isFork = false;
$handlerName = 'BEAR_Resource_Server_Handler';
$server->start($port, $isFork, $handlerName);
