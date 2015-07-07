<?php
// PHP Documentor形式の表記法
/**
* The short description
*
* As many lines of extendend description as you want {@link element} links to an element
* {@link http://www.example.com Example hyperlink inline link} links to a website. The inline
* source tag displays function source code in the description:
* {@source }
*
* In addition, in version 1.2+ one can link to extended documentation like this
* documentation using {@tutorial phpDocumentor/phpDocumentor.howto.pkg}
* In a method/class var, {@inheritdoc may be used to copy documentation from}
* the parent method
* {@internal
* This paragraph explains very detailed information that will only
* be of use to advanced developers, and can contain
* {@link http://www.example.com Other inline links!} as well as text}}
*
* Here are the tags:
*
* @abstract
* @access       public or private
* @author       author name <author@email>
* @copyright    name date
* @deprecated   description
* @deprec       alias for deprecated
* @example      /path/to/example
* @exception    Javadoc-compatible, use as needed
* @global       type $globalvarname
  or
* @global       type description of global variable usage in a function
* @ignore
* @internal     private information for advanced developers only
* @param        type [$varname] description
* @return       type description
* @link         URL
* @name         procpagealias
  or
* @name         $globalvaralias
* @magic        phpdoc.de compatibility
* @package      package name
* @see          name of another element that can be documented, produces a link to it in the documentation
* @since        a version or a date
* @static
* @staticvar    type description of static variable usage in a function
* @subpackage    sub package name, groupings inside of a project
* @throws       Javadoc-compatible, use as needed
* @todo         phpdoc.de compatibility
* @var        type    a data type for a class variable
* @version    @package_version@
*
*
* -----------------------------------------
*
* Example 1. Simple setOption() example
* <code>
* $db->setOption('autofree', true);
* </code>
*
* Example 2. Portability for lowercasing and trimming
* <code>
* $db->setOption('portability',
*  DB_PORTABILITY_LOWERCASE | DB_PORTABILITY_RTRIM);
* </code>
*/

/**
 * DocBlock Description details
<b> -- emphasize/bold text
<code> -- Use this to surround php code, some converters will highlight it
<br> -- hard line break, may be ignored by some converters
<i> -- italicize/mark as important
<kbd> -- denote keyboard input/screen display
<li> -- list item
<ol> -- ordered list
<p> -- If used to enclose all paragraphs, otherwise it will be considered text
<pre> -- Preserve line breaks and spacing, and assume all tags are text (like XML's CDATA)
<samp> -- denote sample or examples (non-php)
<ul> -- unordered list
<var> -- denote a variable name
*/

//サンプル
?>