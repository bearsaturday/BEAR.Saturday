<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 */

/**
 * ビュー
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 *
 * @Singleton
 *
 * @config string adapter     ビューアダプタークラス
 * @config bool   ua_sniffing UAスニッフィング？
 */
class BEAR_View extends BEAR_Factory
{
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * テンプレート名の取得
     *
     * @return array
     *
     * @todo Smarty以外のViewアダプタ
     */
    public function factory()
    {
        $options = $this->_config['enable_ua_sniffing'] ? array('injector' => 'onInjectUaSniffing') : array();
        // 'BEAR_View_Smarty_Adapter' as default
        return BEAR::factory($this->_config['adapter'], $this->_config, $options);
    }
}
