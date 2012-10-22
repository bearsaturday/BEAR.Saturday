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
 * @version   CVS: $Id: XML.php 233111 2007-04-02 09:38:10Z fredericpoeydome $
 * @link      http://pear.php.net/package/Var_Dump
 */

/**
 * Include the base class for all renderers
 */

require_once 'Var_Dump/Renderer/Common.php';

/**
 * A concrete renderer for Var_Dump
 *
 * Returns a representation of a variable in XML
 * DTD : PEARDIR/data/Var_Dump/renderer-xml.dtd
 *
 * @category  PHP
 * @package   Var_Dump
 * @author    Frederic Poeydomenge <fpoeydomenge@free.fr>
 * @copyright 1997-2006 The PHP Group
 * @license   http://www.php.net/license/3_0.txt PHP License 3.0
 * @version   CVS: $Id: XML.php 233111 2007-04-02 09:38:10Z fredericpoeydome $
 * @link      http://pear.php.net/package/Var_Dump
 */

class Var_Dump_Renderer_XML extends Var_Dump_Renderer_Common
{

    /**
     * Class constructor.
     *
     * @param array $options Parameters for the rendering.
     * @access public
     */
    function Var_Dump_Renderer_XML($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Returns the string representation of a variable
     *
     * @return string The string representation of the variable.
     * @access public
     */
    function toString()
    {
        if (count($this->family) == 1) {
            return $this->_toString_Single();
        } else {
            return $this->_toString_Array();
        }
    }

    /**
     * Returns the string representation of a single variable
     *
     * @return string The string representation of a single variable.
     * @access private
     */
    function _toString_Single()
    {
        return
            '<element>' .
            '<type>' . htmlspecialchars($this->type[0]) . '</type>' .
            '<value>' . htmlspecialchars($this->value[0]) . '</value>' .
            '</element>';
    }

    /**
     * Returns the string representation of a multiple variable
     *
     * @return string The string representation of a multiple variable.
     * @access private
     */
    function _toString_Array()
    {
        $txt = '';
        $counter = count($this->family);
        $depth = 0;
        for ($c = 0 ; $c < $counter ; $c++) {
            switch ($this->family[$c]) {
                case VAR_DUMP_START_GROUP :
                    if ($this->depth[$c] > 0) {
                        $txt .=
                            $this->_spacer($depth) . '<type>group</type>' . "\n" .
                            $this->_spacer($depth++) . '<value>' . "\n";
                    }
                    $txt .=
                        $this->_spacer($depth) .
                        '<group caption="' . htmlspecialchars($this->value[$c]) .
                        '">' . "\n";
                    break;
                case VAR_DUMP_FINISH_GROUP :
                    $txt .= $this->_spacer($depth) . '</group>' . "\n";
                    if ($this->depth[$c] > 0) {
                        $txt .=
                            $this->_spacer(--$depth) . '</value>' . "\n" .
                            $this->_spacer(--$depth) . '</element>' . "\n";
                        $depth--;
                    }
                    break;
                case VAR_DUMP_START_ELEMENT_NUM :
                case VAR_DUMP_START_ELEMENT_STR :
                    $txt .=
                        $this->_spacer(++$depth) . '<element>' . "\n" .
                        $this->_spacer(++$depth) . '<key>' .
                        htmlspecialchars($this->value[$c]) .
                        '</key>' . "\n";
                    break;
                case VAR_DUMP_FINISH_ELEMENT :
                case VAR_DUMP_FINISH_STRING :
                    $txt .=
                        $this->_spacer($depth) . '<type>' .
                        htmlspecialchars($this->type[$c]) .
                        '</type>' . "\n".
                        $this->_spacer($depth--) . '<value>' .
                        htmlspecialchars($this->value[$c]) .
                        '</value>' . "\n" .
                        $this->_spacer($depth--) . '</element>' . "\n";
                    break;
            }
        }
        return $txt;
    }

    /**
     * Returns a spacer string to prefix the line
     *
     * @param integer $depth Depth level.
     * @return string Spacer string
     * @access private
     */
    function _spacer($depth) {
        return str_repeat(' ', $depth << 1);
    }

}

?>