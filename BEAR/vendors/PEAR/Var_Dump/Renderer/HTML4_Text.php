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
 * @version   CVS: $Id: HTML4_Text.php 233111 2007-04-02 09:38:10Z fredericpoeydome $
 * @link      http://pear.php.net/package/Var_Dump
 */

/**
 * Include Text Renderer class
 */

require_once 'Var_Dump/Renderer/Text.php';

/**
 * A concrete renderer for Var_Dump
 *
 * Returns a text representation of a variable in HTML 4
 * Extends the 'Text' renderer, with just a predefined set of options,
 * that are empty by default. You can also directly call the 'Text' renderer
 * with the corresponding configuration options.
 *
 * @category  PHP
 * @package   Var_Dump
 * @author    Frederic Poeydomenge <fpoeydomenge@free.fr>
 * @copyright 1997-2006 The PHP Group
 * @license   http://www.php.net/license/3_0.txt PHP License 3.0
 * @version   CVS: $Id: HTML4_Text.php 233111 2007-04-02 09:38:10Z fredericpoeydome $
 * @link      http://pear.php.net/package/Var_Dump
 */

class Var_Dump_Renderer_HTML4_Text extends Var_Dump_Renderer_Text
{

    /**
     * Class constructor.
     *
     * @param array $options Parameters for the rendering.
     * @access public
     */
    function Var_Dump_Renderer_HTML4_Text($options = array())
    {
        // See Var_Dump/Renderer/Text.php for the complete list of options
        $this->defaultOptions = array_merge(
            $this->defaultOptions,
            array(
                'is_html'      => TRUE,
                'before_text'  => '<pre>',
                'after_text'   => '</pre>',
                'before_type'  => '<font color="#006600">',
                'after_type'   => '</font>',
                'before_value' => '<font color="#339900">',
                'after_value'  => '</font>'
            )
        );
        $this->setOptions($options);
    }

}

?>