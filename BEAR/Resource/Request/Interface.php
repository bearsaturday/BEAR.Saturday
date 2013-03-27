<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Interface.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * リソースリクエストインターフェイス
 *
 * 実行クラスはBEAR/Resource/Request/Excecute/以下に配置します。
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Interface.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */
interface BEAR_Resource_Request_Interface
{
    /**
     * リソースリクエスト
     *
     * @param string $method  メソッド
     * @param string $uri     URI
     * @param array  $values  引数
     * @param array  $options オプション
     *
     * @return mixed
     */
    public function request($method, $uri, array $values = array(), array $options = array());
}
