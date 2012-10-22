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
 * @version   SVN: Release: $Id$
 * @link      http://api.bear-project.net/BEAR_Client/BEAR_Client.html
 */
/**
 * BEAR_Client
 *
 * PC以外のクラインとを扱います。携帯やiPhone等
 *
 * @category  BEAR
 * @package   BEAR_Client
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id$
 * @link      http://api.bear-project.net/BEAR_Client/BEAR_Client.html
 */
class BEAR_Client extends BEAR_Factory
{

    /**
     * キャッシュなし
     */
    const ADAPTOR_NONE = 0;

    /**
     * Cache_Lite
     */
    const ADAPTOR_MOBILE = 1;

    /**
     * memcahced
     */
    const ADAPTOR_IHPONE = 2;

    /**
     * APC
     */
    const ADAPTOR_DSI = 3;

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
     * ファクトリー
     *
     * 指定のアダプターでオブジェクトを返します
     *
     * @param void
     *
     * @return BEAR_Client_Adapter
     */
    public function factory()
    {
        if (isset($this->_config['adaptor'])) {
            $instance = BEAR::dependency('BEAR_Client_Adaptor_' . $this->_config['adaptor'], $this->_config);
        } else {
            $instance = BEAR::dependency('BEAR_Client_Adaptor_Default', $this->_config);
        }
        return $instance;
    }
}