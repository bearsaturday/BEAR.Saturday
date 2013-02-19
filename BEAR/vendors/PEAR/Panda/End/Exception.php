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
 * Panda_End_Exception
 *
 * throw this exception instead of exit()
 *
 */
class Panda_End_Exception extends Exception
{
    /**
     * Object
     *
     * @var object
     */
    private $_obj;

    /**
     * Constructor
     *
     * @param object
     * 
     * @return void
     */
    public function __construct($obj = null)
    {
        $this->_obj = $obj;
    }

    /**
     * get object
     * 
     */
    public function getObject(){
        return $this->_obj;
    }
}