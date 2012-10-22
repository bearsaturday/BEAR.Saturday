<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Apc.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 */
/**
 * APCキャッシュクラス
 *
 * <pre>
 * BEAR_Cache_Adapter抽象クラスをPECL::apcをエンジンで実装しています。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Apc.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 *  */
class BEAR_Cache_Adapter_Apc extends BEAR_Cache_Adapter
{

    /**
     * インスタンス取得
     *
     * @param array $options オプション
     *
     * @return object
     * @see http://jp.php.net/manual/ja/function.Apc-addserver.php
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $app = BEAR::get('app');
        $this->_config['info'] = $config['info'];
        if (!extension_loaded('apc') || !(ini_get('apc.enabled')) || !function_exists('apc_sma_info')) {
            throw $this->_exception('APC extention is not loaded');
        } else {
            if ($this->_config['debug']) {
                $apcSmaInfo = apc_sma_info();
                $this->_log->log('APC', $apcSmaInfo);
            }
        }
    }

    /**
     * キャッシュを保存
     *
     * @param string $key   キャッシュキー
     * @param mixed  $value 値
     *
     * @return bool
     */
    public function set($key, $value)
    {
        $result = apc_store($this->_config['info']['id'] . $this->_config['info']['version'] . $key, $value, $this->_life);
        $this->_log->log('APC[W]', array('key' => $key, 'result' => $result));
        return $result;
    }

    /**
     * キャッシュを取得
     *
     * キーを基にキャッシュデータを取得します
     *
     * @param string $key     キー
     * @param mixed  $default デフォルト
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $result = apc_fetch($this->_config['info']['id'] . $this->_config['info']['version'] . $key);
        if (!$result) {
            $result = $default;
        }
        if ($result) {
            $this->_log->log('Apc[R]', $key);
        }
        return $result;
    }

    /**
     * キャッシュの削除
     *
     * @param string $key キー
     *
     * @return bool
     */
    public function delete($key)
    {
        $result = apc_delete($this->_config['info']['id'] . $this->_config['info']['version'] . $key);
        return $result;
    }

    /**
     * キャッシュの全削除
     *
     * @return bool
     *
     */
    public function deleteAll()
    {
        return apc_clear_cache('user');
    }

    /**
     * キャッシュ生存時間を決める
     *
     * <pre>
     * 無制限にしたいときはは0ではなくnullです。
     * ０を指定すると最小キャッシュ時間がセットされます。
     * これはCache_Liteとインターフェイスを合わせるためです。
     * </pre>
     *
     * @param mixed $life 秒 nullで無期限
     *
     * @return void
     */
    public function setLife($life = null)
    {
        if ($life == null) {
            $life = 0;
        } elseif ($life == 0) {
            $life = 1;
        }
        $this->_life = $life;
    }
}