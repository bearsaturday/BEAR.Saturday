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
 * @version   SVN: Release: $Id: Lite.php 871 2009-09-12 20:52:15Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 */
/**
 * Cahce Liteキャッシュクラス
 *
 * <pre>
 * BEAR_Cache_Adapter抽象クラスをPEAR::Cache_Liteをエンジンに実装しています。
 * </pre
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Lite.php 871 2009-09-12 20:52:15Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 */
class BEAR_Cache_Adapter_Lite extends BEAR_Cache_Adapter
{

    /**
     * インスタンス取得
     *
     * @return object
     * @see http://jp.php.net/manual/ja/function.memcache-addserver.php
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $options = array('cacheDir' => _BEAR_APP_HOME . '/tmp/cache_lite/',
            'automaticSerialization' => true,
            'automaticCleaningFactor' => 100);
        // _adapterをCache_Liteに
        $this->_adapter = BEAR::dependency('Cache_Lite', $options);
        /* @var $this->_adapter Cache_Lite */
    }

    /**
     * キャッシュを取得
     *
     * キーを基にキャッシュデータを取得します
     *
     * @param string $key     キー
     * @param mixed  $options オプション
     *
     * @return mixed
     */
    public function get($key, $options = array('default' => null))
    {
        $result = $this->_adapter->get($key . $this->_config['info']['version']);
        // 結果がなくてデファルトが用意されていればデフェオルト
        if ($result === false && $options['default']) {
            $result = $options['default'];
        }
        if ($result !== false) {
            $this->_log->log('Cache Lite[R]', $key);
        }
        return $result;
    }

    /**
     * キャッシュを保存
     *
     * @param string $key    キー
     * @param mixed  $values 値
     *
     * @return bool
     */
    public function set($key, $values)
    {
        $result = $this->_adapter->save($values, $key . $this->_config['info']['version']);
        $this->_log->log('Cache Lite[W]', array('key' => $key, 'result' => $result));
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
        $result = $this->_adapter->remove($key . $this->_config['info']['version']);
        $this->_log->log('Cache Lite[D]', $key);
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
        $result = $this->_adapter->clean();
        return $result;
    }

    /**
     * キャッシュ生存時間を決める
     *
     * @param mixed $life 秒 nullで無期限
     *
     * @return void
     */
    public function setLife($life = null)
    {
        $this->_adapter->setLifeTime($life);
    }
}