<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * リソースリクエストインターフェイス
 *
 * <pre>
 * 実行クラスはBEAR/Resource/Request/Excecute/以下に配置します。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
interface BEAR_Resource_Request_Interface
{

    /**
     * リソースリクエスト
     *
     * @param string $method メソッド
     * @param string $uri    URI
     * @param array  $values 引数
     * @param string $method メソッド
     *
     * @return mixed
     */
    public function request($method, $uri, array $values = array(), array $options = array());
}