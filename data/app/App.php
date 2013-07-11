<?php
/**
 * App
 *
 * @category BEAR
 * @package  BEAR.app
 * @author   $Author:$ <username@example.com>
 * @license  @license@ http://@license_url@
 * @version  Release: @package_version@ $Id:$
 * @link     http://@link_url@
 */

/**
 * App root path
 */
define('_BEAR_APP_HOME', realpath(dirname(__FILE__)));
require_once 'BEAR.php';

$bearMode = isset($_SERVER['bearmode']) ? $_SERVER['bearmode'] : 0;
// profile
//include 'BEAR/Dev/Profile/script/startxh.php'; //xhprof

App::init($bearMode);

/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Db
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */
class App
{
    /**
     * App init
     *
     * @param int $bearMode
     *
     * @return void
     */
    public static function init($bearMode = 1)
    {
        $app = BEAR::loadConfig(_BEAR_APP_HOME . '/App/app.yml');
        switch ($bearMode) {
            case 1 :
                //debug mode (cache disabled)
                $app['BEAR_Cache']['adapter'] = 0;
            case 2 :
                //debug mode (cache enabled)
                $app['core']['debug'] = true;
                $app['App_Db']['dsn']['default'] = $app['App_Db']['dsn']['slave'] = $app['App_Db']['dsn']['test'];
                $app['BEAR_Ro_Prototype']['__class'] = 'BEAR_Ro_Prototype_Debug';
                break;
            case 100:
                // for UNIT test or HTTP access test
                $app['core']['debug'] = true;
                $app['App_Db']['dsn']['default'] = $app['App_Db']['dsn']['slave'] = $app['App_Db']['dsn']['test'];
                $app['BEAR_Log']['__class'] = 'BEAR_Log_Test';
                $app['BEAR_Resource_Request']['__class'] = 'BEAR_Resource_Request_Test';
                break;
            case 0 :
            default :
                // live
                $app['core']['debug'] = false;
                break;
        }
        BEAR::init($app);
    }
}