<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Main
 * @subpackage Injector
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2017 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 */

/**
 * Main UA インジェクト
 *
 * @category   BEAR
 * @package    BEAR_Main
 * @subpackage Injector
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2017 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 */
class BEAR_Main_Ua_Injector implements BEAR_Injector_Interface
{
    /**
     * UAインジェクト
     *
     * @param BEAR_Main &$object BEAR_Mainオブジェクト
     * @param array     $config  設定
     *
     * @return void
     */
    public static function inject($object, $config)
    {
        $userAgent = isset($config['http_user_agent']) ? $config['http_user_agent'] : (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $agent = BEAR::dependency('BEAR_Agent', array('user_agent' => $userAgent));
        /** @var $agent BEAR_Agent */
        $object->setService('_agent', $agent);
        $object->setConfig('ua', $agent->getUa());
        $object->setConfig('enable_ua_sniffing', true);
        //エージェント依存サブミット（絵文字、コード）
        $agent->adapter->submit();
    }
}
