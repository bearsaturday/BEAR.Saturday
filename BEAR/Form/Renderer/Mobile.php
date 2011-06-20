<?php
/**
 * Replacement for the default renderer of HTML_QuickForm that uses only XHTML
 * and CSS but no table tags, and generates fully valid XHTML output
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * Copyright (c) 2005-2007, Mark Wiesemann <wiesemann@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category HTML
 * @package  HTML_QuickForm_Renderer_Tableless
 * @author   Alexey Borzov <borz_off@cs.msu.su>
 * @author   Adam Daniel <adaniel1@eesus.jnj.com>
 * @author   Bertrand Mansion <bmansion@mamasam.com>
 * @author   Mark Wiesemann <wiesemann@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version  SVN: $Id:$
 * @link     http://pear.php.net/package/HTML_QuickForm_Renderer_Tableless
 * @ignore
 */


/**
 * Replacement for the default renderer of HTML_QuickForm that uses only XHTML
 * and CSS but no table tags, and generates fully valid XHTML output
 *
 * You need to specify a stylesheet like the one that you find in
 * data/stylesheet.css to make this work.
 *
 * @category HTML
 * @package  HTML_QuickForm_Renderer_Tableless
 * @author   Alexey Borzov <borz_off@cs.msu.su>
 * @author   Adam Daniel <adaniel1@eesus.jnj.com>
 * @author   Bertrand Mansion <bmansion@mamasam.com>
 * @author   Mark Wiesemann <wiesemann@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version  Release: 0.6.1
 * @link     http://pear.php.net/package/HTML_QuickForm_Renderer_Tableless
 * @ignore
 */
class BEAR_Form_Renderer_Mobile extends HTML_QuickForm_Renderer_Tableless
{
    /**
     * Header Template string
     * @var      string
     * @access   private
     */
    var $_headerTemplate = "\n<div style=\"color:#fff;background:#2c6ebd;font-size:medium;text-align:left;\">{header}</div>\n";

    /**
     * Element template string
     * @var      string
     * @access   private
     */
    var $_elementTemplate = "\n\t\t<label><!-- BEGIN required --><span style=\"color:red;\">*</span><!-- END required -->{label}</label><div class=\"element<!-- BEGIN error -->_error<!-- END error -->\"><!-- BEGIN error --><div style=\"color:#ff0000\">{error}</div><!-- END error --><!-- BEGIN label_2 --><div style=\"color:#555;font-size:xx-small;\">{label_2}</div><!-- END label_2 -->{element}<!-- BEGIN label_3 -->{label_3}<!-- END label_3 --></div>";

    /**
     * Form template string
     * @var      string
     * @access   private
     */
    var $_formTemplate = "\n<div style=\"color:#000000;background:#eeeeaa;\"><form{attributes}>\n\t<div style=\"display: none;\">\n{hidden}\t</div>\n{content}\n</form></div>";

    /**
     * Template used when opening a fieldset
     * @var      string
     * @access   private
     */
    //    var $_openFieldsetTemplate = "\n\t<fieldset{id}{attributes}>";
    var $_openFieldsetTemplate = "";

    /**
     * Template used when opening a hidden fieldset
     * (i.e. a fieldset that is opened when there is no header element)
     * @var      string
     * @access   private
     */
    //    var $_openHiddenFieldsetTemplate = "\n\t<fieldset class=\"hidden{class}\">\n\t\t<ol>";
    var $_openHiddenFieldsetTemplate = "";

    /**
     * Template used when closing a fieldset
     * @var      string
     * @access   private
     */
    //    var $_closeFieldsetTemplate = "\n\t\t</ol>\n\t</fieldset>";
    var $_closeFieldsetTemplate = "";

    /**
     * Required Note template string
     * @var      string
     * @access   private
     */
    var $_requiredNoteTemplate =
        "\n\t\t\t<div><span style=\"color:black;font-size:xx-small;text-align:left;\">{requiredNote}</span></div>";

    /**
     * Constructor
     *
     * @access public
     */
    function __construct()
    {
        $this->HTML_QuickForm_Renderer_Default();
    }
}