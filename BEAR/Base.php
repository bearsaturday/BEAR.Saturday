<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Base.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * BEARクラスの抽象クラス
 *
 * <pre>
 * BEARフレームワークで使われる基底クラスです。
 *
 * BEARのクラスはBEAR::factoryやBEAR::dependency()によって以下の順でインスタンス化されます。
 *
 * 1) コンストラクタで設定を行う
 * 2) 設定に基づいてインジェクタで必要なサービス（オブジェクト、変数）をプロパティを用意する
 *
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Base.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 * @abstract
 */
abstract class BEAR_Base implements BEAR_Base_Interface
{

    /**
     * Class config
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Universal constructor
     *
     * 設定を_configプロパティに代入します。
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * Inject
     *
     * 設定にしたがってサービスをインジェクトします。
     * サービスオブジェクトは通常BEAR::get()やBEAR::dependency()を使ってサービスロケータで取得します。
     *
     * @return void
     */
    public function onInject()
    {
    }

    /**
     * Set config
     *
     * @param mixed $config (string)設定キー | (array)設定配列
     * @param mixed $values (string)$configの時の設定値
     *
     * @return self
     */
    public function setConfig($config, $values = null)
    {
        if (is_string($config)) {
            $this->_config[$config] = $values;
        } else {
            $this->_config = $config;
        }
        return $this;
    }

    /**
     * Get config
     *
     * @param string $key 設定キー、指定なければ全ての設定を取得
     *
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if (isset($key)) {
            return $this->_config[$key];
        } else {
            return $this->_config;
        }
    }

    /**
     * Set service
     *
     * @param string $name    サービスキー
     * @param object $service サービス
     *
     * @return void
     */
    public function setService($name, $service)
    {
        $this->$name = $service;
    }

    /**
     * 例外の作成
     *
     * @param string $msg    例外メッセージ
     * @param array  $config 例外config
     *
     * @return BEAR_Exception
     */
    protected function _exception($msg, array $config = array())
    {
        $class = get_class($this) . '_Exception';
        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        if (!file_exists(_BEAR_APP_HOME . "/{$file}") && !file_exists(_BEAR_BEAR_HOME . "/{$file}")) {
            $class = 'BEAR_Exception';
        }
        return new $class($msg, $config);
    }
}