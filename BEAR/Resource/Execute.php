<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Execute.php 2503 2011-06-11 10:09:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * リソースリクエスト実行
 *
 * リソースリクエストを実行するクラスです。
 * URIによってどの方法で実行するかををfacotryで判断しています。
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Execute.php 2503 2011-06-11 10:09:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Execute extends BEAR_Factory
{
    /**
     * HTTPリソース
     */
    const FORMAT_HTTP = 'Http';

    /**
     * RO（クラス）リソース
     */
    const FORMAT_RO = 'Ro';

    /**
     * スタティックファイルリソース
     */
    const FORMAT_FILE = 'File';

    /**
     * ソケットリソース
     */
    const FORMAT_SOCKET = 'Socket';

    /**
     * ページリソース
     */
    const FORMAT_PAGE = 'Page';

    /**
     * ファクトリー
     *
     * URIによってリソースリクエスト実行クラスを確定して
     * インジェクションオブジェクトを生成します
     *
     * @param array $config
     *
     * @return BEAR_Resource_Execute_Interface
     * @throws BEAR_Resource_Execute_Exception
     */
    public function factory()
    {
        // モック
        if (isset($this->_config['options']['mock']) && $this->_config['options']['mock']) {
            return BEAR::factory('BEAR_Resource_Execute_Mock', $this->_config);
        }
        // パス情報も見て実行ファイルを決定
        $url = parse_url($this->_config['uri']);
        $path = pathinfo($this->_config['uri']);
        // スタティックバリューファイル　file:///var/data/data.ymlなど
        if (isset($url['scheme']) && ($url['scheme'] === 'file')) {
            $exeConfig = $this->_config;
            $exeConfig['url'] = $url;
            $exeConfig['path'] = $path;
            $exeConfig['file'] = $url['path'];
            $format = self::FORMAT_FILE;
            $obj = BEAR::factory('BEAR_Resource_Execute_' . $format, $exeConfig);
            return $obj;
        }
        if (isset($path['filename']) && !(isset($url['host']))) {
            return self::_localResourceExecute($this->_config['uri']);
        }
        $executer = _BEAR_APP_HOME . '/App/Resource/Execute/' . ucwords($url['scheme']) . '.php';
        $isExecuterExists = file_exists($executer);
        if ($isExecuterExists) {
            $class = 'App_Resource_Execute_' . ucwords($url['scheme']);
            $obj = BEAR::factory($class, $this->_config);
            return $obj;
        }
        switch (true) {
            case (isset($url['scheme']) && ($url['scheme'] == 'http' || $url['scheme'] == 'https')) :
                $format = self::FORMAT_HTTP;
                break;
            case (isset($url['scheme']) && $url['scheme'] == 'socket') :
                $format = self::FORMAT_SOCKET;
                break;
            case (isset($url['scheme']) && $url['scheme'] == 'page') :
                $format = self::FORMAT_PAGE;
                break;
            default :
                $msg = 'URI is not valid.';
                $info = array('uri' => $this->_config['uri']);
                throw $this->_exception($msg, comact('info'));
        }
        $obj = BEAR::factory('BEAR_Resource_Execute_' . $format, $this->_config);
        return $obj;
    }

    /**
     * ローカルリソースの実行
     *
     * ローカルリソースファイル(Ro, Function, スタティックファイル等）を実行します。
     *
     * @param string $uri
     *
     * @return stdClass
     * @throws BEAR_Resource_Exception
     */
    private function _localResourceExecute($uri)
    {
        $file = _BEAR_APP_HOME . '/App/Ro/' .$uri . '.php';
        if (file_exists($file)) {
            include_once $file;
            $resourcePathName = 'App_Ro_' . str_replace('/', '_', $uri);
            switch (true) {
                case (class_exists($resourcePathName, false)) :
                    $this->_config['class'] = $resourcePathName;
                    $format = 'Ro';
                    break;
                case (function_exists($resourcePathName)) :
                    //@deprecated
                    $this->_config['function'] = $resourcePathName;
                    $format = 'Function';
                    break;
                default :
                    $msg = 'Mismatch resource class/function error.（ファイル名とクラス/関数名がミスマッチです。)';
                    $info = array(
                    'resource name' => $resourcePathName,
                    'resource file' => $file);
                    throw $this->_exception(
                        $msg,
                        array(
                            'code' => BEAR::CODE_BAD_REQUEST,
                            'info' => $info)
                    );
                    $format = 'Mock';
            }
        } else {
            $file = _BEAR_APP_HOME . '/App/Ro/' . $uri;
            if (file_exists($file)) {
                $this->_config['file'] = $file;
                $format = self::FORMAT_FILE;
            } else {
                throw $this->_exception(
                    "Resource file[{$file}] is not exists.",
                    array('info' => array('uri' => $uri, 'file' => $file))
                );
                $format = 'Null';
            }
        }
        $obj = BEAR::factory('BEAR_Resource_Execute_' . $format, $this->_config);
        return $obj;
    }
}
