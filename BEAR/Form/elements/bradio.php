<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Form
 * @subpackage Element
 * @author     Adam Daniel <adaniel1@eesus.jnj.com>
 * @author     Bertrand Mansion <bmansion@mamasam.com>
 * @author     Alexey Borzov <avb@php.net>
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$ Form.php 1308 2010-01-03 16:20:02Z koriyama@bear-project.net $
 * @link       http://pear.php.net/package/HTML_QuickForm
 * @link       http://www.bear-project.net/
 */

/**
 * Base class for <input /> form elements
 */
/** @noinspection PhpIncludeInspection */
require_once 'HTML/QuickForm/input.php';

/**
 * HTML class for a "bradio" element
 *
 * @category   BEAR
 * @package    BEAR_Form
 * @subpackage Element
 * @author     Adam Daniel <adaniel1@eesus.jnj.com>
 * @author     Bertrand Mansion <bmansion@mamasam.com>
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @version    SVN: Release: @package_version@ $Id:$ Form.php 1308 2010-01-03 16:20:02Z koriyama@bear-project.net $
 * @link       http://pear.php.net/package/HTML_QuickForm
 * @since      1.0
 */
class HTML_QuickForm_bradio extends HTML_QuickForm_radio
{
    // {{{ properties

    /**
     * Radio display text
     * @var       string
     * @since     1.1
     * @access    private
     */
    var $_text = '';

    // }}}
    // {{{ constructor

    /**
     * Constructor
     *
     * @param null $elementName
     * @param null $elementLabel
     * @param null $text
     * @param null $value
     * @param null $attributes
     */
    function HTML_QuickForm_bradio($elementName=null, $elementLabel=null, $text=null, $value=null, $attributes=null)
    {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        if (isset($value)) {
            $this->setValue($value);
        }
        $this->_persistantFreeze = true;
        $this->setType('radio');
        $this->_text = $text;
        $this->_generateId();
    } //end constructor

    // }}}
    // {{{ setChecked()


    /**
     * Returns the radio element in HTML
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function toHtml()
    {
        if (0 == strlen($this->_text)) {
            $label = '';
        } elseif ($this->_flagFrozen) {
            $label = $this->getChecked() ? $this->_text : '';
            //            $label = $this->_text;
        } else {
            $label = '<label for="' . $this->getAttribute('id') . '">' . $this->_text . '</label>';
        }
        return HTML_QuickForm_input::toHtml() . $label;
    } //end func toHtml

    // }}}
    // {{{ getFrozenHtml()

    /**
     * Returns the value of field without HTML tags
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getFrozenHtml()
    {
        if ($this->getChecked()) {
            return '' .
            $this->_getPersistantData();
        } else {
            return '';
        }
    } //end func getFrozenHtml

}