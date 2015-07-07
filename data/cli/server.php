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
 * @version    SVN: Release: @package_version@ $Id: server.php 2549 2011-06-13 23:39:37Z akihito.koriyama@gmail.com $ server.php 1510 2010-04-08 17:21:24Z koriyama@users.sourceforge.jp $
 */

require_once '../App.php';
// BEARサーバースタート
$server = new BEAR_Resource_Server(array());
$port = 13754;
$isFork = false;
$handlerName = 'BEAR_Resource_Server_Handler';
$server->start($port, $isFork, $handlerName);
