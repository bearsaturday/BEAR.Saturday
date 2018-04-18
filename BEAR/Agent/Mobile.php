<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Mobileエージェント
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
        $reporting = error_reporting(E_ALL & ~E_STRICT);
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $netUserAgentMobile = Net_UserAgent_Mobile::factory($userAgent);
        error_reporting($reporting);
        /* @noinspection PhpDynamicAsStaticMethodCallInspection */
        if (PEAR::isError($netUserAgentMobile)) {
            $reporting = error_reporting(E_ALL & ~E_STRICT);
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $netUserAgentMobile = Net_UserAgent_Mobile::factory('');
            error_reporting($reporting);
        }

        return $netUserAgentMobile;
    }
}
