<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */

/**
 * キャッシュ
 *
 * <pre>
 * ビルトインアダプター
 *
 * 0 ADAPTER_NONE      キャッシュなし
 * 1 ADAPTER_CACHELITE PEAR::Cache_Lite
 * 2 ADAPTER_MEMCACHE  MEMCACHE
 * 3 ADAPTER_APC       APC
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 * @see       PECL::Memcache, PEAR::Cache_Lite
 *
 * @config mixed adapter キャッシュアダプター,integerならビルトイン、stringならユーザー定義クラス
 */
class BEAR_Cache extends BEAR_Factory
{
    /**
     * キャッシュなし
     */
    const ADAPTER_NONE = 0;

    /**
     * Cache_Lite
     */
    const ADAPTER_CACHELITE = 1;

    /**
     * memcahced
     */
    const ADAPTER_MEMCACHE = 2;

    /**
     * APC
     */
    const ADAPTER_APC = 3;

    /**
     * キャッシュライフタイム無期限
     *
     */
    const LIFE_UNLIMITED = null;

    /**
     * キャッシュライフタイムなし
     *
     */
    const LIFE_NONE = 0;

    /**
     * キャッシュキープレフィックス
     *
     * @var string
     */
    protected $_prefix;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['prefix'] = isset($this->_config['prefix']) ? $this->_config['prefix'] : ($this->_config['info']['id'] . $this->_config['info']['version'] . (int) $this->_config['debug']);
        include_once 'MDB2.php'; // PEAR::MDB2
    }

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
    }

    /**
     * キャッシュファクトリー
     *
     * 指定のキャッシュアダプターでキャッシュオブジェクトを返します
     *
     * @return BEAR_Cache_Adapter
     */
    public function factory()
    {
        switch ($this->_config['adapter']) {
            case self::ADAPTER_MEMCACHE:
                $instance = BEAR::dependency('BEAR_Cache_Adapter_Memcache', $this->_config);
                break;
            case self::ADAPTER_CACHELITE:
                $instance = BEAR::dependency('BEAR_Cache_Adapter_Lite', $this->_config);
                break;
            case self::ADAPTER_APC:
                $instance = BEAR::dependency('BEAR_Cache_Adapter_Apc', $this->_config);
                break;
            default:
                if (is_string($this->_config['adapter'])) {
                    $instance = BEAR::dependency('App_Cache_Adapter_' . $this->_config['adapter']);
                    break;
                }
                $instance = BEAR::dependency('BEAR_Cache_Adapter_Void', $this->_config);
        }

        return $instance;
    }
}
