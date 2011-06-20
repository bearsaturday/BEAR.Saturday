<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Aspect
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */

/**
 * Transaction advice
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Aspect
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */
class App_Aspect_Transaction implements BEAR_Aspect_Around_Interface
{
    /**
     * Transaction aroud advice
     *
     * @param array                 $values
     * @param BEAR_Aspect_JoinPoint $joinPoint
     *
     * @return array
     */
    public function around(array $values, BEAR_Aspect_JoinPoint $joinPoint)
    {
        // pre process
        $obj = $joinPoint->getObject();
        $db = $obj->getDb();
        $db->beginTransaction();
        //　proceed original method
        $result = $joinPoint->proceed($values);
        // post process
        if (!MDB2::isError($result)) {
            $db->commit();
        } else {
            $db->rollback();
        }
        return $result;
    }
}