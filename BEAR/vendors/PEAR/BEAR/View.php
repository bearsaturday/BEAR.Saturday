<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Query.php 1021 2009-10-13 04:04:08Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
/**
 * ビューファクトリークラス
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Query.php 1021 2009-10-13 04:04:08Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
class BEAR_View extends BEAR_Factory
{
	/**
	 * コンストラクタ
	 *
	 * @param array $config 設定
	 *
	 * @return void
	 */
	public function __construct(array $config)
	{
		parent::__construct($config);
	}

    /**
     * テンプレート名の取得
     *
     * @param string $tplName テンプレート名（省略可）
     *
     * @return array
     *
     * @todo Smarty以外のViewアダプタ
     */
    public function factory()
    {
    	$options = $this->_config['ua_sniffing'] ? array('injector' =>  'onInjectUaSniffing') : array();
        return BEAR::factory('BEAR_View_Adaptor_Smarty', $this->_config, $options);
    }
}