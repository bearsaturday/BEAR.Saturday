<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
 */
/**
 * エージェントインジェクト
 *
 * <pre>
 * エージェントアダプター抽象クラスです。BEAR/Agent/Adapter/の各クラスで実装します。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
 * @abstract
 *  */
class BEAR_Agent_Injector implements BEAR_Injector_Interface
{
    /**
     *　インジェクト
     *
     * @param BEAR_Agent $object BEAR_Agentオブジェクト
     * @param array      $config 設定
     */
    public static function inject(&$object, $config)
    {
    	$agent = BEAR::dependency('BEAR_Agent');
    	$ua = $agent->getUa();
    	$config = $agent->adaptor->getConfig();
    	$role = $config['role'];
        foreach ($role as $agent) {
        	$method = 'onInject' . $agent;
        	if (method_exists($object, $method)) {
        		$object->$method();
        		break;
        	}
        	$object->onInject();
        }
    }
}