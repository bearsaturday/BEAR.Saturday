<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Main
 * @subpackage Injector
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * Main UA インジェクト
 *
 * @category   BEAR
 * @package    BEAR_Main
 * @subpackage Injector
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
 */
class BEAR_Main_Ua_Injector implements BEAR_Injector_Interface
{
    /**
     * UAインジェクト
     *
     * @param BEAR_Main &$object BEAR_Mainオブジェクト
     * @param array     $config 設定
     *
     * @return void
     */
    public static function inject(&$object, $config)
    {
        $userAgent = isset($config['http_user_agent']) ? $config['http_user_agent'] :
            (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $agent = BEAR::dependency('BEAR_Agent', array('user_agent' => $userAgent));
        $object->setService('_agent', $agent);
        $object->setConfig('ua', $agent->getUa());
        $object->setConfig('enable_ua_sniffing', true);
        //エージェント依存サブミット（絵文字、コード）
        $agent->adaptor->submit();
    }
}