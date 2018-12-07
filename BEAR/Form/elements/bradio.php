<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Base class for <input /> form elements
 */
/** @noinspection PhpIncludeInspection */

/**
 * HTML class for a "bradio" element
 *
 * @link       http://pear.php.net/package/HTML_QuickForm
 * @since      1.0
 */
class HTML_QuickForm_bradio extends HTML_QuickForm_radio
{
    // {{{ properties

    /**
     * Radio display text
     *
     * @var string
     *
     * @since     1.1
     */
    public $_text = '';

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
    public function __construct(
        $elementName = null,
        $elementLabel = null,
        $text = null,
        $value = null,
        $attributes = null
    ) {
        parent::__construct($elementName, $elementLabel, $text, $value, $attributes);
        if (isset($value)) {
            $this->setValue($value);
        }
        $this->_persistantFreeze = true;
        $this->setType('radio');
        $this->_text = $text;
        $this->_generateId();
    }

    //end constructor

    // }}}
    // {{{ setChecked()

    /**
     * Returns the radio element in HTML
     *
     * @since     1.0
     *
     * @return string
     */
    public function toHtml()
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
    }

    //end func toHtml

    // }}}
    // {{{ getFrozenHtml()

    /**
     * Returns the value of field without HTML tags
     *
     * @since     1.0
     *
     * @return string
     */
    public function getFrozenHtml()
    {
        if ($this->getChecked()) {
            return '' . $this->_getPersistantData();
        }

        return '';
    }

    //end func getFrozenHtml
}
