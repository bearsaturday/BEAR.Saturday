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
 * Hello World 2
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Page
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */
class Page_Test_HelloWorld2 extends App_Page
{
    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        parent::onInject();
        $this->injectGet('id', 'id', 0);
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
        $uri = 'Test/User';
        $params = array(
            'uri' => 'Test/User',
            'values' => array('id' => $args['id']),
            'options' => array('template' => 'test/greeting')
        );
        $this->_resource->read($params)->set('greeting');
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

App_Main::run('Page_Test_HelloWorld2', array(), array('injector' => 'onInjectUA'));