<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * リソースリクエストインターフェイス
 *
 * 実行クラスはBEAR/Resource/Request/Excecute/以下に配置します。
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
