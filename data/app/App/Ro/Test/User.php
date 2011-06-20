<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Ro
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */

/**
 * User
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Ro
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */
class App_Ro_Test_User extends App_Ro
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
     * data
     *
     * @var array
     */
    protected static $userData = array(
        array('id' => 0, 'name' => 'World'),
        array('id' => 1, 'name' => 'BEAR')
    );

    /**
     * Read
     *
     * @required id
     *
     * @return array
     */
    public function onRead($values)
    {
        $id = $values['id'];
        $this->assert(isset(self::$userData[$id]));
        return self::$userData[$id];
    }

}