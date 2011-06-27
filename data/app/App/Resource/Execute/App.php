<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Resource
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */

/**
 * スタティクファイルリソース
 *
 * アプリケーション作製のリソースサンプルです。
 * app://self/path/to/fileと指定されたファイルの中身をリソースbodyとして扱います。
 * ML, YAML, CSV, INIファイルをサポートしています。
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Resource
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */
class App_Resource_Execute_App extends BEAR_Resource_Execute_Adapter
{
    /**
     * Constructor
     *
     * @param array $config
     *
     * @config string  'method' アクセスメソッド
     * @config string  'uri'    URI
     * @config array   'values' パラメータ
     * @config array   'optins' オプション
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * リソースアクセス
     *
     * @return mixed
     *
     * @throws BEAR_Resource_Exception 読めなかった時の例外
     */
    public function request()
    {
        // read only
        if ($this->_config['method'] === BEAR_Resource::METHOD_READ) {
            $file = str_replace('app:/', '', $this->_config['uri']);
            $result = file_get_contents($file);
        } else {
            $config = array('info' => compact('method'), 'code' => 400);
            throw new BEAR_Resource_Exception('Method not allowed', $config);
        }
        return $result;
    }
}