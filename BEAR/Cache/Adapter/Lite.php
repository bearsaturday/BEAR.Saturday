<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Cache
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2017 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 */

/**
 * PEAR::Cache_Liteアダプター
 *
 * @category   BEAR
 * @package    BEAR_Cache
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2017 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 *
 * @Singleton
 *
 * @config
 */
class BEAR_Cache_Adapter_Lite extends BEAR_Cache_Adapter
{
    /**
     * Constructor取得
     *
     * @param array $config
     *
     * @see http://jp.php.net/manual/ja/function.memcache-addserver.php
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $options = array(
            'cacheDir' => _BEAR_APP_HOME . '/tmp/cache_lite/',
            'automaticSerialization' => true,
            'automaticCleaningFactor' => 100
        );
        // _adapterをCache_Liteに
        $this->_adapter = BEAR::dependency('Cache_Lite', $options);
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
        $result = $this->_adapter->get($this->_config['prefix'] . $key);
        // 結果がなくてデフォルトが用意されていればデフォルト
        if ($result === false && $options['default']) {
            $result = $options['default'];
        }
        if ($result instanceof BEAR_Ro_Container) {
            $ro = BEAR::factory('BEAR_Ro');
            /** @var $ro BEAR_Ro */
            $ro->setCode($result->code)->setHeaders((array) $result->header)->setBody($result->body)->setLinks(
                $result->links
            )->setHtml($result->html);

            return $ro;
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
        if ($values instanceof BEAR_Ro) {
            $values = new BEAR_Ro_Container($values);
        }
        $result = $this->_adapter->save($values, $this->_config['prefix'] . $key);
        $log = array('key' => $key, 'result' => $result);
        $this->_log->log('Cache Lite[W]', $log);

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
        $result = $this->_adapter->remove($this->_config['prefix'] . $key);
        $this->_log->log('Cache Lite[D]', $key);

        return $result;
    }

    /**
     * キャッシュの全削除
     *
     * @return bool
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
     * @return BEAR_Cache_Adapter_Lite
     */
    public function setLife($life = null)
    {
        $this->_adapter->setLifeTime($life);

        return $this;
    }
}
