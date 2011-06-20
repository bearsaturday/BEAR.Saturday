<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Page
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */

require_once 'App.php';

/**
 * Hello World
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Page
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */
class Page_HelloWorld extends App_Page
{
    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
    }

    /**
     * Init
     *
     * @param array $args
     *
     * @return void
     */
    public function onInit(array $args)
    {
        $this->set('greeting', 'hello world');
    }

    /**
     * Output
     *
     * @return void
     */
    public function onOutput()
    {
        $this->display('helloWorld.tpl');
    }
}

App_Main::run('Page_HelloWorld');