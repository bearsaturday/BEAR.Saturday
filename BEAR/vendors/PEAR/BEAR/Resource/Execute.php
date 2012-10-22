<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Execute.php 1255 2009-12-07 08:04:17Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * リソース実行クラス
 *
 * <pre>
 * リソースリクエストを実行するクラスです。どの方法で実行するかををfacotryで判断しています。
 * DIコンテナでBEAR_Resource_Requestクラスに注入されます。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Execute.php 1255 2009-12-07 08:04:17Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
class BEAR_Resource_Execute extends BEAR_Factory
{

    /**
     * HTTPリソース
     */
    CONST FORMAT_HTTP = 'Http';

    /**
     * RO（クラス）リソース
     */
    CONST FORMAT_RO = 'Ro';

    /**
     * スタティックファイルリソース
     */
    CONST FORMAT_FILE = 'StaticFile';

    /**
     * ソケットリソース
     */
    CONST FORMAT_SOCKET = 'Socket';

    /**
     * ページリソース
     */
    CONST FORMAT_PAGE = 'Page';

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
     * ファクトリー
     *
     * <pre>
     * URIによってリソースリクエスト実行クラスを確定して
     * インジェクションオブジェクトを生成します
     * </pre>
     *
     * @param array $config コンフィグ
     *
     * @return void
     */
    public function factory()
    {
        // モック
        if (isset($this->_config['options']['mock']) && $this->_config['options']['mock']) {
            return BEAR::factory('BEAR_Resource_Execute_Mock', $this->_config);
        }
        // パス情報も見て実行ファイルを決定
        $exeConfig['url'] = $url = parse_url($this->_config['uri']);
        $exeConfig['path'] = $path = pathinfo($this->_config['uri']);
        // スタティックバリューファイル　file:///var/data/data.ymlなど
        if (isset($url['scheme']) && ($url['scheme'] === 'file')) {
            $exeConfig['file'] = $url['path'];
            $format = self::FORMAT_FILE;
            $obj = BEAR::factory('BEAR_Resource_Execute_' . $format, $exeConfig);
            return $obj;
        }
        if (isset($path['filename']) && !(isset($url['host']))) {
            return self::_localResourceExecute($this->_config['uri']);
        } else {
            //ファイルでない
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
                    $executer = _BEAR_APP_HOME . '/App/Resource/Execute/' . ucwords($url['scheme']) . '.php';
                    $isExecuterExists = file_exists($executer);
                    if ($isExecuterExists){
                        $class = 'App_Resource_Execute_' . ucwords($url['scheme']);
                        $obj = BEAR::factory($class, $this->_config);
                        return $obj;
                    } else {
                    $msg = 'URI is not valid.';
                        $info = array(
                        'uri' => $this->_config['uri']);
                        throw $this->_exception($msg, array(
                        'info' => $info));
                    }
            }
        }
        $obj = BEAR::factory('BEAR_Resource_Execute_' . $format, $this->_config);
        return $obj;
    }

    /**
     * ローカルリソースの実行
     *
     * <pre>
     * ローカルのリソースファイル(Vo, Function, スタティックファイル等）の
     * リソースファイルを実行します。
     * </pre>
     *
     * @param array $config 設定
     *
     * @return object
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
                    throw $this->_exception($msg, array(
                    'code' => BEAR::CODE_BAD_REQUEST,
                    'info' => $info));
                    $format = 'Mock';
            }
        } else {
            $file = _BEAR_APP_HOME . '/App/Ro/' . $uri;
            if (file_exists($file)) {
                $this->_config['file'] = $file;
                $format = self::FORMAT_FILE;
            } else {
                throw $this->_exception('Resource file is not exists.', array('info'=>array('uri'=>$uri, 'file'=>$file)));
                $format = 'Null';
            }
        }
        $obj = BEAR::factory('BEAR_Resource_Execute_' . $format, $this->_config);
        return $obj;
    }
}