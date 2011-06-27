<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Query.php 1021 2009-10-13 04:04:08Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * ビュー
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Query.php 1021 2009-10-13 04:04:08Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
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