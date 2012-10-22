<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2006 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Frederic Poeydomenge <fpoeydomenge@free.fr>                 |
// +----------------------------------------------------------------------+

/**
 * Wrapper for the var_dump function.
 *
 * " The var_dump function displays structured information about expressions
 * that includes its type and value. Arrays are explored recursively
 * with values indented to show structure. "
 *
 * The Var_Dump class captures the output of the var_dump function,
 * by using output control functions, and then uses external renderer
 * classes for displaying the result in various graphical ways :
 * simple text, HTML/XHTML text, HTML/XHTML table, XML, ...
 *
 * @category  PHP
 * @package   Var_Dump
 * @author    Frederic Poeydomenge <fpoeydomenge@free.fr>
 * @copyright 1997-2006 The PHP Group
 * @license   http://www.php.net/license/3_0.txt PHP License 3.0
 * @version   CVS: $Id: Renderer.php 233111 2007-04-02 09:38:10Z fredericpoeydome $
 * @link      http://pear.php.net/package/Var_Dump
 */

/**
 * A loader class for the renderers.
 *
 * @category  PHP
 * @package   Var_Dump
 * @author    Frederic Poeydomenge <fpoeydomenge@free.fr>
 * @copyright 1997-2006 The PHP Group
 * @license   http://www.php.net/license/3_0.txt PHP License 3.0
 * @version   CVS: $Id: Renderer.php 233111 2007-04-02 09:38:10Z fredericpoeydome $
 * @link      http://pear.php.net/package/Var_Dump
 */

class Var_Dump_Renderer
{

    /**
     * Attempt to return a concrete Var_Dump_Renderer instance.
     *
     * @param string $mode Name of the renderer.
     * @param array $options Parameters for the rendering.
     * @access public
     */
    function & factory($mode, $options)
    {
        @include_once 'Var_Dump/Renderer/' . $mode . '.php';
        $className = 'Var_Dump_Renderer_' . $mode;
        if (class_exists($className)) {
            $obj = new $className($options);
        } else {
            include_once 'PEAR.php';
            PEAR::raiseError('Var_Dump: renderer "' . $mode . '" not found', TRUE);
            return NULL;
        }
        return $obj;
    }

}

?>