<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Injector.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link      https://github.com/bearsaturday
 */

/**
 * UAインジェクター
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   Release: @package_version@ $Id: Injector.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net
 */
class BEAR_Agent_Injector implements BEAR_Injector_Interface
{
    /**
     *　Inject
     *
     * @param BEAR_Agent &$object BEAR_Agentオブジェクト
     * @param array $config
     *
     * @return void
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
