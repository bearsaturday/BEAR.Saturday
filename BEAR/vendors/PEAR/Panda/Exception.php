<?php
/**
 * Panda
 *
 * PHP versions 5
 *
 * @category  Panda
 * @package   Panda
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id$
 */

/**
 * Panda_Exeception
 *
 * Produce http status screen
 *
 */
class Panda_Exception extends ErrorException
{
    /**
     * Error info
     */
    protected $_info;

    /**
     * Constructor
     *
     * @param int    $httpStatus HTTP code
     * @param string $message    messsage
     * @param int    $severity   severity
     *
     * @return void
     */
    public function __construct($message, $httpStatus = 200, array $info = array())
    {
        $trace = debug_backtrace();
        $filename = $trace[0]['file'];
        $lineno = $trace[0]['line'];
        parent::__construct($message, $httpStatus, 0, $filename, $lineno);
        $this->_info = $info;
    }

    /**
     * Get error info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->_info;
    }
}