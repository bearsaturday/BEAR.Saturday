<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Injector
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * UA判別
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Injector
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
 */
class BEAR_Agent_Ua implements BEAR_Injector_Interface
{
    /**
     * UAインジェクト
     *
     * 携帯３キャリア/iPhone/iPad/Andoroidの判定を行います。
     * BEAR_Agentの$config['ua_inject']でこのクラスが指定されています。
     *
     * @param BEAR_Main &$object BEAR_Agentオブジェクト
     * @param array     $config 設定
     *
     * @return void
     * @see http://code.google.com/p/bear-project/wiki/agent
     */
    public static function inject($object, $config)
    {
        if (!isset($config['user_agent'])) {
            $object->setService('_ua', BEAR_Agent::UA_DEFAULT);
            return;
        }
        $agentMobile = BEAR::dependency('BEAR_Agent_Mobile', array('user_agent' => $config['user_agent']));
        if ($agentMobile->isNonMobile()) {
            if (strpos($config['user_agent'], 'iPhone') !== false) {
                // iPhoneの場合
                $ua = BEAR_Agent::UA_IPHONE;
            } else if (strpos($config['user_agent'], 'iPad') !== false) {
                // iPadの場合
                $ua = BEAR_Agent::UA_IPAD;
            } else if (strpos($config['user_agent'], 'Android') !== false) {
                // Androidの場合
                $ua = BEAR_Agent::UA_ANDROID;
            } else {
                $ua = BEAR_Agent::UA_DEFAULT;
            }
        } else {
            $ua = ucwords(strtolower($agentMobile->getCarrierLongName()));
        }
        $object->setService('_ua', $ua);
    }
}