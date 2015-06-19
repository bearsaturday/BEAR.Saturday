<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * Mobileエージェント
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
 */
class BEAR_Agent_Mobile extends BEAR_Factory
{
    /**
     * ボット用携帯エージェント(Docomo)
     */
    const BOT_DOCOMO = 'DoCoMo/2.0 N900i(c100;TB;W24H12)';

    /**
     * ボット用携帯エージェント(Docomo)
     */
    const BOT_AU = 'KDDI-TS26 UP.Browser/6.2.0.5 (GUI) MMP/1.1';

    /**
     * ボット用携帯エージェント(SB)
     */
    const BOT_SOFTBANK = 'SoftBank/1.0/705P/PJA23 Browser/Teleca-Browser/3.1 Profile/MIDP-2.0 Configuration/CLDC-1.1';

    /**
     * ファクトリー
     *
     * @return Net_UserAgent_Mobile
     */
    public function factory()
    {
        $userAgent = $this->_config['user_agent'];
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $netUserAgentMobile = Net_UserAgent_Mobile::factory($userAgent);
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        if (PEAR::isError($netUserAgentMobile)) {
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $netUserAgentMobile = Net_UserAgent_Mobile::factory('');
        }

        return $netUserAgentMobile;
    }
}
