<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Cache
 * @subpackage Adapter
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Void.php 2534 2011-06-12 15:34:47Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * Voidアダプター
 *
 * キャッシュが無効になります
 *
 * @category   BEAR
 * @package    BEAR_Cache
 * @subpackage Adapter
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Void.php 2534 2011-06-12 15:34:47Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 *
 * @Singleton
 */
final class BEAR_Cache_Adapter_Void extends BEAR_Base
{
    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * キャッシュ無効
     *
     * @param string $name キー
     * @param mixed  $args 値
     *
     * @return bool
     */
    public function __call($name, $args)
    {
        if ($this->_config['debug']) {
            $log = array('name' => $name, 'args' => $args);
            $this->_log->log('BEAR_Cache_Adapter_None', $log);
        }
        return null;
    }

    /**
     * Set Life
     *
     * @param int $life
     *
     * @return BEAR_Cache_Adapter_None
     */
    public function setLife($life = null)
    {
        return $this;
    }
}