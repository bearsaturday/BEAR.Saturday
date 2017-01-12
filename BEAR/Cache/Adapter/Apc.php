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
 * APCアダプター
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
 */
class BEAR_Cache_Adapter_Apc extends BEAR_Cache_Adapter
{
    /**
     * Constructor
     *
     * @param array $config
     *
     * @see http://jp.php.net/manual/ja/function.Apc-addserver.php
     * @throws BEAR_Cache_Exception
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['info'] = $config['info'];
        if (!extension_loaded('apc') || !(ini_get('apc.enabled')) || !function_exists('apc_sma_info')) {
            throw new BEAR_Cache_Exception('APC extention is not loaded');
        } else {
            if ($this->_config['debug']) {
                $apcSmaInfo = apc_sma_info();
                /** @noinspection PhpUndefinedMethodInspection */
                BEAR::dependency('BEAR_Log')->log('APC', $apcSmaInfo);
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
        $result = apc_store($this->_config['prefix'] . $key, $value, $this->_life);
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
        $result = apc_fetch($this->_config['prefix'] . $key);
        if ($result === false && !is_null($default)) {
            $result = $default;
        }
        if ($result instanceof BEAR_Ro_Container) {
            $ro = BEAR::factory('BEAR_Ro');
            /** @var $ro BEAR_Ro */
            $ro->setCode($result->code)->setHeaders((array) $result->header)->setBody($result->body)->setLinks(
                $result->links
            )->setHtml($result->html);
            $result = $ro;
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
        $result = apc_delete($this->_config['prefix'] . $key);

        return $result;
    }

    /**
     * キャッシュの全削除
     *
     * @return bool
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
     * @return BEAR_Cache_Adapter_Apc
     */
    public function setLife($life = null)
    {
        if ($life == null) {
            $life = 0;
        } elseif ($life == 0) {
            $life = 1;
        }
        $this->_life = $life;

        return $this;
    }
}
