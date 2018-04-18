<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * UAインジェクター
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 */
class BEAR_Agent_Injector implements BEAR_Injector_Interface
{
    /**
     *　Inject
     *
     * @param BEAR_Agent &$object BEAR_Agentオブジェクト
     * @param array      $config
     */
    public static function inject($object, $config)
    {
        $agent = BEAR::dependency('BEAR_Agent');
        $config = $agent->adapter->getConfig();
        $role = $config['role'];
        foreach ($role as $agent) {
            $method = 'onInject' . $agent;
            if (method_exists($object, $method)) {
                $object->$method();

                return;
            }
        }
        $object->onInject();
    }
}
