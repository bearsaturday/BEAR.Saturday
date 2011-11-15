<?php
// vim: set expandtab tabstop=4 shiftwidth=4 fdm=marker:

/**
 * XML RSS parser
 *
 * PHP version 4
 *
 * @category XML
 * @package  XML_RSS
 * @author   Martin Jansen <mj@php.net>
 * @license  PHP License http://php.net/license
 * @version  SVN: $Id: RSS.php 310689 2011-05-01 17:54:48Z kguest $
 * @link     http://pear.php.net/package/XML_RSS
 */

require_once 'XML/Parser.php';

/**
 * RSS parser class.
 *
 * This class is a parser for Resource Description Framework (RDF) Site
 * Summary (RSS) documents. For more information on RSS see the
 * website of the RSS working group (http://www.purl.org/rss/).
 *
 * @category XML
 * @package  XML_RSS
 * @author   Martin Jansen <mj@php.net>
 * @license  PHP License http://php.net/license
 * @link     http://pear.php.net/package/XML_RSS
 */
class XML_RSS extends XML_Parser
{
    // {{{ properties

    /**
     * @var string
     */
    var $insideTag = '';

    /**
     * @var array
     */
    var $insideTagStack = array();

    /**
     * @var string
     */
    var $activeTag = '';

    /**
     * @var array
     */
    var $channel = array();

    /**
     * @var array
     */
    var $items = array();

    /**
     * @var array
     */
    var $item = array();

    /**
     * @var array
     */
    var $image = array();

    /**
     * @var array
     */
    var $textinput = array();

    /**
     * @var array
     */
    var $textinputs = array();

    /**
     * @var array
     */
    var $attribs;

    /**
     * @var array
     */
    var $parentTags = array('CHANNEL', 'ITEM', 'IMAGE', 'TEXTINPUT');

    /**
     * @var array
     */
    var $channelTags = array(
        'TITLE', 'LINK', 'DESCRIPTION', 'IMAGE',
        'ITEMS', 'TEXTINPUT', 'LANGUAGE', 'COPYRIGHT',
        'MANAGINGEditor', 'WEBMASTER', 'PUBDATE', 'LASTBUILDDATE',
        'CATEGORY', 'GENERATOR', 'DOCS', 'CLOUD', 'TTL',
        'RATING'
    );

    /**
     * @var array
     */
    var $itemTags = array(
        'TITLE', 'LINK', 'DESCRIPTION', 'PUBDATE', 'AUTHOR', 'CATEGORY',
        'COMMENTS', 'ENCLOSURE', 'GUID', 'SOURCE',
        'CONTENT:ENCODED'
    );

    /**
     * @var array
     */
    var $imageTags = array('TITLE', 'URL', 'LINK', 'WIDTH', 'HEIGHT');


    var $textinputTags = array('TITLE', 'DESCRIPTION', 'NAME', 'LINK');

    /**
     * List of allowed module tags
     *
     * Currently supported:
     *
     *   Dublin Core Metadata
     *   blogChannel RSS module
     *   CreativeCommons
     *   Content
     *   Syndication
     *   Trackback
     *   GeoCoding
     *   Media
     *   iTunes
     *
     * @var array
     */
    var $moduleTags = array(
        'DC:TITLE', 'DC:CREATOR', 'DC:SUBJECT', 'DC:DESCRIPTION',
        'DC:PUBLISHER', 'DC:CONTRIBUTOR', 'DC:DATE', 'DC:TYPE',
        'DC:FORMAT', 'DC:IDENTIFIER', 'DC:SOURCE', 'DC:LANGUAGE',
        'DC:RELATION', 'DC:COVERAGE', 'DC:RIGHTS',
        'BLOGCHANNEL:BLOGROLL', 'BLOGCHANNEL:MYSUBSCRIPTIONS',
        'BLOGCHANNEL:BLINK', 'BLOGCHANNEL:CHANGES',
        'CREATIVECOMMONS:LICENSE', 'CC:LICENSE', 'CONTENT:ENCODED',
        'SY:UPDATEPERIOD', 'SY:UPDATEFREQUENCY', 'SY:UPDATEBASE',
        'TRACKBACK:PING', 'GEO:LAT', 'GEO:LONG',
        'MEDIA:GROUP', 'MEDIA:CONTENT', 'MEDIA:ADULT',
        'MEDIA:RATING', 'MEDIA:TITLE', 'MEDIA:DESCRIPTION',
        'MEDIA:KEYWORDS', 'MEDIA:THUMBNAIL', 'MEDIA:CATEGORY',
        'MEDIA:HASH', 'MEDIA:PLAYER', 'MEDIA:CREDIT',
        'MEDIA:COPYRIGHT', 'MEDIA:TEXT', 'MEDIA:RESTRICTION',
        'ITUNES:AUTHOR', 'ITUNES:BLOCK', 'ITUNES:CATEGORY',
        'ITUNES:DURATION', 'ITUNES:EXPLICIT', 'ITUNES:IMAGE',
        'ITUNES:KEYWORDS', 'ITUNES:NEW-FEED-URL', 'ITUNES:OWNER',
        'ITUNES:PUBDATE', 'ITUNES:SUBTITLE', 'ITUNES:SUMMARY'
    );

    /**
     * @var array
     */
    var $last = array();

    // }}}
    // {{{ Constructor

    /**
     * Constructor
     *
     * @param mixed  $handle File pointer, name of the RSS file, or an RSS string.
     * @param string $srcenc Source charset encoding, use null (default)
     *                       to use default encoding (ISO-8859-1)
     * @param string $tgtenc Target charset encoding, use null (default)
     *                       to use default encoding (ISO-8859-1)
     *
     * @return void
     * @access public
     */
    function XML_RSS($handle = '', $srcenc = null, $tgtenc = null)
    {
        if ($srcenc === null && $tgtenc === null) {
            $this->XML_Parser();
        } else {
            $this->XML_Parser($srcenc, 'event', $tgtenc);
        }

        $this->setInput($handle);

        if ($handle == '') {
            $this->raiseError('No input passed.');
        }
    }

    // }}}
    // {{{ startHandler()

    /**
     * Start element handler for XML parser
     *
     * @param object $parser  XML parser object
     * @param string $element XML element
     * @param array  $attribs Attributes of XML tag
     *
     * @return void
     * @access private
     */
    function startHandler($parser, $element, $attribs)
    {
        if (substr($element, 0, 4) == "RSS:") {
            $element = substr($element, 4);
        }

        switch ($element) {
        case 'CHANNEL':
        case 'ITEM':
        case 'IMAGE':
        case 'TEXTINPUT':
            $this->insideTag = $element;
            array_push($this->insideTagStack, $element);
            break;
        
        case 'ENCLOSURE' :
            $this->attribs = $attribs;
            break;
            
        default:
            $this->activeTag = $element;
        }
    }

    // }}}
    // {{{ endHandler()

    /**
     * End element handler for XML parser
     *
     * If the end of <item>, <channel>, <image> or <textinput>
     * is reached, this method updates the structure array
     * $this->struct[] and adds the field "type" to this array,
     * that defines the type of the current field.
     *
     * @param object $parser  XML parser object
     * @param string $element Name of element that ends
     *
     * @return void
     * @access private
     */
    function endHandler($parser, $element)
    {
        if (substr($element, 0, 4) == "RSS:") {
            $element = substr($element, 4);
        }

        if ($element == $this->insideTag) {
            array_pop($this->insideTagStack);
            $this->insideTag = end($this->insideTagStack);

            $this->struct[] = array_merge(
                array('type' => strtolower($element)),
                $this->last
            );
        }

        if ($element == 'ITEM') {
            $this->items[] = $this->item;
            $this->item = '';
        }

        if ($element == 'IMAGE') {
            $this->images[] = $this->image;
            $this->image = '';
        }

        if ($element == 'TEXTINPUT') {
            $this->textinputs = $this->textinput;
            $this->textinput = '';
        }

        if ($element == 'ENCLOSURE') {
            if (!isset($this->item['enclosures'])) {
                $this->item['enclosures'] = array();
            }

            $this->item['enclosures'][] = array_change_key_case(
                $this->attribs, CASE_LOWER
            );
            $this->attribs = array();
        }

        $this->activeTag = '';
    }

    // }}}
    // {{{ cdataHandler()

    /**
     * Handler for character data
     *
     * @param object $parser XML parser object
     * @param string $cdata  CDATA
     *
     * @return void
     * @access private
     */
    function cdataHandler($parser, $cdata)
    {
        if (in_array($this->insideTag, $this->parentTags)) {
            $tagName = strtolower($this->insideTag);
            $var = $this->{$tagName . 'Tags'};

            if (in_array($this->activeTag, $var)
                || in_array($this->activeTag, $this->moduleTags)
            ) {
                $this->_add(
                    $tagName, strtolower($this->activeTag),
                    $cdata
                );
            }

        }
    }

    // }}}
    // {{{ defaultHandler()

    /**
     * Default handler for XML parser
     *
     * @param object $parser XML parser object
     * @param string $cdata  CDATA
     *
     * @return void
     * @access private
     */
    function defaultHandler($parser, $cdata)
    {
        return;
    }

    // }}}
    // {{{ _add()

    /**
     * Add element to internal result sets
     *
     * @param string $type  Name of the result set
     * @param string $field Fieldname
     * @param string $value Value
     *
     * @return void
     * @access private
     * @see    cdataHandler
     */
    function _add($type, $field, $value)
    {
        if ($field == 'category') {
            $this->{$type}[$field][] = $value;
        } else if (empty($this->{$type}) || empty($this->{$type}[$field])) {
            $this->{$type}[$field] = $value;
        } else {
            $this->{$type}[$field] .= $value;
        }

        $this->last = $this->{$type};
    }

    // }}}
    // {{{ getStructure()

    /**
     * Get complete structure of RSS file
     *
     * @return array
     * @access public
     */
    function getStructure()
    {
        return (array)$this->struct;
    }

    // }}}
    // {{{ getchannelInfo()

    /**
     * Get general information about current channel
     *
     * This method returns an array containing the information
     * that has been extracted from the <channel>-tag while parsing
     * the RSS file.
     *
     * @return array
     * @access public
     */
    function getChannelInfo()
    {
        return (array)$this->channel;
    }

    // }}}
    // {{{ getItems()

    /**
     * Get items from RSS file
     *
     * This method returns an array containing the set of items
     * that are provided by the RSS file.
     *
     * @return array
     * @access public
     */
    function getItems()
    {
        return (array)$this->items;
    }

    // }}}
    // {{{ getImages()

    /**
     * Get images from RSS file
     *
     * This method returns an array containing the set of images
     * that are provided by the RSS file.
     *
     * @return array
     * @access public
     */
    function getImages()
    {
        return (array)$this->images;
    }

    // }}}
    // {{{ getTextinputs()

    /**
     * Get text input fields from RSS file
     *
     * @return array
     * @access public
     */
    function getTextinputs()
    {
        return (array)$this->textinputs;
    }

    // }}}

}
?>
