<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Base.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Aspect/BEAR_Aspect.html
 */
/**
 * BEARクラスの抽象クラス
 *
 * <pre>
 * BEARで使われるクラスの基底クラスです。
 * コンストラクタによるconfigの読み取りとセットを行います。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Base.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR/BEAR.html
 * @abstract
 *
 */
abstract class BEAR_Base implements BEAR_Base_Interface
{
    /**
     * リダイレクトオプション - 終了
     *
     * @var bool
     */
    const CONFIG_END = false;

    /**
     * コンフィグ
     *
     * @var array
     */
    protected $_config = array();

    /**
     * ログオブジェクト
     *
     * @var BEAR_Log
     */
    protected $_log;

    /**
     * コンストラクタ.
     *
     * $configが配列なら$_configプロパティとマージされます。
     * 文字列ならそれをファイルパスとして読み込み初期値とします。
     *
     * @param mixed $config ユーザー設定値
     *
     * @return void
     *
     */
    public function __construct(array $config)
    {
        $app = BEAR::get('app');
        $class = get_class($this);
        if (isset($app[$class])) {
            $config = array_merge($app[$class], $config);
        }
        $this->_config = (array)$config;
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * コンフィグセット
     *
     * @param array $config 設定
     *
     * @return void
     */
    public function setConfig(array $config)
    {
        $this->_config = $config;
    }

    /**
     * コンフィグセット（個別）
     *
     * @param string $key   コンフィグキー
     * @param mixed  $value コンフィグ値
     *
     * @return void
     */
    public function setConfigVal($key, $value)
    {
        $this->_config[$key] = $value;
    }

    public function getConfigVal($keye)
    {
        return $this->_config[$key];
    }
    
    /**
     * コンフィグ取得
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    public function setService($name, $service)
    {
        $this->$name = $service;
    }
    
    /**
     * 例外の作成
     *
     * @param string $msg
     * @param array $config
     *
     * @return BEAR_Exception
     */
    function _exception($msg, array $config = array())
    {
        $class = get_class($this) . '_Exception';
        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        if (!file_exists(_BEAR_APP_HOME . "/{$file}") && !file_exists(_BEAR_BEAR_HOME . "/{$file}")) {
            $class = 'BEAR_Exception';
        }
        return new $class($msg, $config);
    }
}