<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   Dev
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: AutoLoader.php 2551 2011-06-14 09:32:14Z koriyama@bear-project.net $
 * @link      http://api.bear-project.net/
 */

require_once 'Net/Growl.php';

/**
 * Dev Autoloader (PSR-0)
 *
 * alternative autolodar for dev
 *
 * @category  BEAR
 * @package   Dev
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: AutoLoader.php 2551 2011-06-14 09:32:14Z koriyama@bear-project.net $
 * @link      http://api.bear-project.net/
 *
 * @config  void
 **/
class BEAR_Dev_AutoLoader
{
    public static function onAutoload($class)
    {
        static $growlApp;

        BEAR::onAutoload($class);
        $ref = new ReflectionClass($class);
        $file = $ref->getFileName();

        if (!$growlApp) {
            $growlApp = new Net_Growl_Application(__CLASS__, array("Growl_Notify"));
        }
        $growl = Net_Growl::singleton($growlApp, null, null);
        $growl->setNotificationLimit(16);
        if (BEAR::exists($class)) {
            $config = BEAR::get($class)->getConfig();
        }
        $growl->notify("Growl_Notify", $class, $class);



    }
}
