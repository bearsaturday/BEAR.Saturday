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
 * @version   SVN: Release: $Id: Cache.php 952 2009-09-22 05:45:23Z koriyama.sourceforge $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 */
/**
 * BEAR_Cache
 *
 * 指定したキャッシュアダプターでキャッシュオブジェクトを生成するファクトリークラスです。
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Cache.php 952 2009-09-22 05:45:23Z koriyama.sourceforge $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 * @see       PECL::Memcache, PEAR::Cache_Lite
 */
class BEAR_Cache extends BEAR_Factory
{

    /**
     * キャッシュなし
     */
    const ADAPTOR_NONE = 0;

    /**
     * Cache_Lite
     */
    const ADAPTOR_CACHELITE = 1;

    /**
     * memcahced
     */
    const ADAPTOR_MEMCACHE = 2;

    /**
     * APC
     */
    const ADAPTOR_APC = 3;

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

    protected $_logs = array('r' =>array(), 'w'=>array());

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
     * キャッシュファクトリー
     *
     * 指定のキャッシュアダプターでキャッシュオブジェクトを返します
     *
     * @param void
     *
     * @return BEAR_Cache_Adapter
     */
    public function factory()
    {
        switch ($this->_config['adaptor']) {
        case self::ADAPTOR_MEMCACHE :
            $instance = BEAR::dependency('BEAR_Cache_Adapter_Memcache', $this->_config);
            break;
        case self::ADAPTOR_CACHELITE :
            $instance = BEAR::dependency('BEAR_Cache_Adapter_Lite', $this->_config);
            break;
        case self::ADAPTOR_APC :
            $instance = BEAR::dependency('BEAR_Cache_Adapter_Apc', $this->_config);
            break;
        default :
            if (is_string($this->_config['adaptor'])) {
                self::$_instance = BEAR::dependency('App_Cache_Adapter_' . $this->_config['adaptor']);
                break;
            }
            $instance = BEAR::dependency('BEAR_Cache_Adapter_None', $this->_config);
        }
        return $instance;
    }
}