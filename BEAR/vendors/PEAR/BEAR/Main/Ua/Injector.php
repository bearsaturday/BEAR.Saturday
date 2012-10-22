<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Main
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Main/BEAR_Main.html
 */
/**
 * Main UA インジェクト
 *
 * <pre>
 * BEAR_Mainにユーザーエージェントを注入します。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Main
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Main/BEAR_Main.html
 * @abstract
 *  */
class BEAR_Main_Ua_Injector implements BEAR_Injector_Interface
{
    /**
     * UAインジェクト
     *
     * @param BEAR_Main $object BEAR_Mainオブジェクト
     * @param array     $config 設定
     */
    public static function inject(&$object, $config)
    {
        $userAgent = isset($config['http_user_agent']) ? $config['http_user_agent'] : $_SERVER['HTTP_USER_AGENT'];
        $agent = BEAR::dependency('BEAR_Agent', array('user_agent' => $userAgent));
        $object->setService('_agent', $agent);
        $object->setConfigVal('ua', $agent->getUa());
        $object->setConfigVal('enable_ua_sniffing', true);
        //エージェント依存サブミット（絵文字、コード）
        $agent->adaptor->submit();
    }
}