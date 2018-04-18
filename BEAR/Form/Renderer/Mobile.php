<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Replacement for the default renderer of HTML_QuickForm that uses only XHTML
 * and CSS but no table tags, and generates fully valid XHTML output
 *
 * You need to specify a stylesheet like the one that you find in
 * data/stylesheet.css to make this work.
 *
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 *
 * @link     http://pear.php.net/package/HTML_QuickForm_Renderer_Tableless
 * @ignore
 */
class BEAR_Form_Renderer_Mobile extends HTML_QuickForm_Renderer_Tableless
{
    /**
     * Header Template string
     *
     * @var string
     */
    public $_headerTemplate = "\n<div style=\"color:#fff;background:#2c6ebd;font-size:medium;text-align:left;\">{header}</div>\n";

    /**
     * Element template string
     *
     * @var string
     */
    public $_elementTemplate = "\n\t\t<label><!-- BEGIN required --><span style=\"color:red;\">*</span><!-- END required -->{label}</label><div class=\"element<!-- BEGIN error -->_error<!-- END error -->\"><!-- BEGIN error --><div style=\"color:#ff0000\">{error}</div><!-- END error --><!-- BEGIN label_2 --><div style=\"color:#555;font-size:xx-small;\">{label_2}</div><!-- END label_2 -->{element}<!-- BEGIN label_3 -->{label_3}<!-- END label_3 --></div>";

    /**
     * Form template string
     *
     * @var string
     */
    public $_formTemplate = "\n<div style=\"color:#000000;background:#eeeeaa;\"><form{attributes}>\n\t<div style=\"display: none;\">\n{hidden}\t</div>\n{content}\n</form></div>";

    /**
     * Template used when opening a fieldset
     *
     * @var string
     */
    //    var $_openFieldsetTemplate = "\n\t<fieldset{id}{attributes}>";
    public $_openFieldsetTemplate = '';

    /**
     * Template used when opening a hidden fieldset
     * (i.e. a fieldset that is opened when there is no header element)
     *
     * @var string
     */
    //    var $_openHiddenFieldsetTemplate = "\n\t<fieldset class=\"hidden{class}\">\n\t\t<ol>";
    public $_openHiddenFieldsetTemplate = '';

    /**
     * Template used when closing a fieldset
     *
     * @var string
     */
    //    var $_closeFieldsetTemplate = "\n\t\t</ol>\n\t</fieldset>";
    public $_closeFieldsetTemplate = '';

    /**
     * Required Note template string
     *
     * @var string
     */
    public $_requiredNoteTemplate = "\n\t\t\t<div><span style=\"color:black;font-size:xx-small;text-align:left;\">{requiredNote}</span></div>";

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->HTML_QuickForm_Renderer_Default();
    }
}
