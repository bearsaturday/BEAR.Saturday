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
 * @version   SVN: Release: $Id: Memcache.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 */
/**
 * Memcacheキャッシュクラス
 *
 * <pre>
 * BEAR_Cache_Adapter抽象クラスをPECL::Memcacheをエンジンに実装しています。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Memcache.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 */
class BEAR_Cache_Adapter_Memcache extends BEAR_Cache_Adapter
{

    /**
     * ポート番号
     */
    static $portMemcached = 11211;

    /**
     * インスタンス取得
     *
     * @return object
     * @see http://jp.php.net/manual/ja/function.memcache-addserver.php
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        if (!extension_loaded('memcache')) {
            throw $this->_exception('Memcached extention is not loaded');
        }
        $this->_adapter = new Memcache();
        //キャッシュサーバー追加
        foreach ($this->_config['path'] as $host) {
            // １台でもconnectでなくaddServerを使う
            $this->_adapter->addServer($host, self::$portMemcached);
        }
        $log = array();
        if ($this->_config['debug']) {
            foreach ($this->_config['path'] as $host) {
                // １台でもconnectでなくaddServerを使う
                $log['status'][$host] = $this->_adapter->getServerStatus($host);
            }
            $log['Ver'] = $this->_adapter->getVersion();
            $this->_log->log('Memcache', $log);
        }
        return $this->_adapter;
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
        $result = $this->_adapter->replace($this->_config['info']['id'] . $this->_config['info']['version'] . $key, $value, MEMCACHE_COMPRESSED, $this->_life);
        if (!$result) {
            $result = $this->_adapter->set($this->_config['info']['id'] . $this->_config['info']['version'] . $key, $value, MEMCACHE_COMPRESSED, $this->_life);
        }
        $this->_log->log('Memcache[W]', array('key' => $key, 'result' => $result));
        return $result;
    }

    /**
     * キャッシュを取得
     *
     * キーを基にキャッシュデータを取得します
     *
     * @param string $key     キー
     * @param mixed  $default 値
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $result = $this->_adapter->get($this->_config['info']['id'] . $this->_config['info']['version'] . $key);
        if (!$result) {
            $result = $default;
        }
        if ($result) {
            $this->_log->log('Memcache[R]', $key);
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
        $result = $this->_adapter->delete($this->_config['info']['id'] . $this->_config['info']['version'] . $key);
        $this->_log->log('Memcache[D]', $key);
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
        return $this->_adapter->flush();
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