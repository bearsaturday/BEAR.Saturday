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
 * Base class
 */
require_once 'HTML/QuickForm/checkbox.php';

/**
 * HTML class for a "bcheckbox" element
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
class HTML_QuickForm_bcheckbox extends HTML_QuickForm_checkbox {

    /**
     * Class constructor
     *
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $elementLabel   (optional)Input field value
     * @param     string    $text           (optional)Checkbox display text
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string
     *                                      or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    public function HTML_QuickForm_bcheckbox($elementName=null, $elementLabel=null, $text='', $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_text = $text;
        $this->setType('checkbox');
        $this->updateAttributes(array('value'=>1));
        $this->_generateId();

    }

    /**
     * Returns the checkbox element in HTML
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
            // チェックの時のみラベルを表示
            $label = $this->getChecked() ? $this->_text : '';
        } else {
            $label = '<label for="' . $this->getAttribute('id') . '">' . $this->_text . '</label>';
        }
        return HTML_QuickForm_input::toHtml() . $label;
    } //end func toHtml


    /**
     * Returns the value of field without HTML tags
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getFrozenHtml() {
        return $this->_getPersistantData();
    }
}
