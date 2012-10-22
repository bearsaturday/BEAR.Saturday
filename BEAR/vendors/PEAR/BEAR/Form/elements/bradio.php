<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Form
 * @author    Adam Daniel <adaniel1@eesus.jnj.com>
 * @author    Bertrand Mansion <bmansion@mamasam.com>
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Form.php 1308 2010-01-03 16:20:02Z koriyama@users.sourceforge.jp $
 * @link      http://pear.php.net/package/HTML_QuickForm
 * @link      http://api.bear-project.net/BEAR_Form/BEAR_Form.html
 */


/**
 * Base class for <input /> form elements
 */
require_once 'HTML/QuickForm/input.php';

/**
 * HTML class for a "bcheckbox" element
 *
 * @category    HTML
 * @package     HTML_QuickForm
 * @author      Adam Daniel <adaniel1@eesus.jnj.com>
 * @author      Bertrand Mansion <bmansion@mamasam.com>
 * @author      Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @version     Release: 3.2.11
 * @since       1.0
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
     * Class constructor
     *
     * @param     string    Input field name attribute
     * @param     mixed     Label(s) for a field
     * @param     string    Text to display near the radio
     * @param     string    Input field value
     * @param     mixed     Either a typical HTML attribute string or an associative array
     * @since     1.0
     * @access    public
     * @return    void
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