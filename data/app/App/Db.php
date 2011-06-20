<?php
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

/**
 * Db
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Db
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 *
 * @config string dsn
 * @config array  option
 */
class App_Db extends BEAR_Factory
{
    /**
     * Db factorys
     *
     * @return array MDB2_Driver_Datatype_mysqli
     */
    public function factory()
    {
        $options['default_table_type'] = 'INNODB';
        $options['use_transactions'] = true;
        $config = array('dsn' => $this->_config['dsn']);
        $config['options'] = $options;
        $db = BEAR::factory('BEAR_Mdb2', $config);
        $db->setOption('quote_identifier', true);
        $db->loadModule('Extended');
        return $db;
    }
}