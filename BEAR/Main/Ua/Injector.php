<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * Main UA インジェクト
 */
class BEAR_Main_Ua_Injector implements BEAR_Injector_Interface
{
    /**
     * UAインジェクト
     *
     * @param BEAR_Main &$object BEAR_Mainオブジェクト
     * @param array     $config  設定
     */
    public static function inject($object, $config)
    {
        $userAgent = isset($config['http_user_agent']) ? $config['http_user_agent'] : (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $agent = BEAR::dependency('BEAR_Agent', array('user_agent' => $userAgent));
        /* @var $agent BEAR_Agent */
        $object->setService('_agent', $agent);
        $object->setConfig('ua', $agent->getUa());
        $object->setConfig('enable_ua_sniffing', true);
        //エージェント依存サブミット（絵文字、コード）
        $agent->adapter->submit();
    }
}
