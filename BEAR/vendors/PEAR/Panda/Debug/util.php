<?php
/**
 * Panda
 *
 * PHP versions 5
 *
 * p - print var
 *
 * <code>
 * p($mixed);
 * p($mixed, 'fire');
 * p($mixed, 'syslog');
 * p($mixed, 'var');
 * </code>
 *
 * t - trace
 *
 * <code>
 * t();
 * tr($a == 1);
 * </code>
 *
 * @category  Panda
 * @package   Panda
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2009 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id$
 * @link      n/a
 *
 * debuglib for PHP5
 * Thomas Schüßler <debuglib at atomar dot de>
 * http://phpdebuglib.de/
 *
 * FireBug
 * http://www.getfirebug.com/
 *
 * FirePHP
 * http://www.christophdorn.com/
 * https://addons.mozilla.org/ja/firefox/addon/6149
 * http://www.firephp.org/
 */

/**
 * Panda Debug Utility
 *

 /**
 * Prints human-readable information about a variable with print location and variable name
 *
 * @param mixed   $mixed   variables
 * @param formart $formart 'var' | 'export' | 'printa' | 'fire' | 'syslog'
 * @param array   $options
 */
function p($mixed = null, $formart = 'dump', array $options = array())
{
    if (PHP_SAPI === 'cli') {
        call_user_func('v', func_get_args());
        return;
    }
    $config = Panda::getConfig();
    if ($config[Panda::CONFIG_DEBUG] !== true) {
        $trace = debug_backtrace();
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        trigger_error('p() is called in no debug mode in ' . $file . ' on line ' .$line, E_USER_WARNING);
        return;
    }
    $options['trace'] = debug_backtrace();
    Panda_Debug::p($mixed, $formart, $options);
}

/**
 * Prints human-readable trace information's link on screen
 *
 * @param bool $condition
 *
 * @return void
 */
function t($condition = true)
{
    if (!$condition) {
        return;
    }
    $config = Panda::getConfig();
    if ($config[Panda::CONFIG_DEBUG] !== true) {
        $trace = debug_backtrace();
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        trigger_error('t() is called in no debug mode in ' . $file . ' on line ' .$line, E_USER_WARNING);
        return;
    }
    $colorCycle = true;
    ob_start();
    debug_print_backtrace();
    $bt = ob_get_clean();
    $bt = str_replace("\n#", "\n##", $bt);
    $btArr = explode("\n#", $bt);
    $bt = '';
    unset($btArr[count($btArr) - 1]);
    foreach ($btArr as $row) {
        $color = $colorCycle ? '#f0f4fc' : "white";
        //          $color = $colorCycle ? '#eaeef6' : "white";
        $colorCycle = !$colorCycle;
        $row = str_replace("\n", '', $row);
        $bt .= '<div style="border-bottom:1px solid #CCCCCC; height:3.25ex; background-color:' . $color . '">' . htmlspecialchars($row) . '</div>';
    }
    echo '<pre><div style="font-size:small;border-color:#e3ecfd;border-width:4px; border-style: solid; -moz-border-radius:4px; -webkit-border-radius
        :4px;">' . $bt . '</div></pre>';
    return;
}

/**
 * Prints human-readable trace information's link on link
 *
 * @param bool $condition
 *
 * @return void
 */
function tr($condition = true)
{
    if (!$condition) {
        return;
    }
    if (function_exists('fb')) {
        fb("tr()", FirePHP::TRACE);
    }
    $config = Panda::getConfig();
    if ($config[Panda::CONFIG_DEBUG] !== true) {
        $trace = debug_backtrace();
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        trigger_error('tr() is called in no debug mode in ' . $file . ' on line ' .$line, E_USER_WARNING);
        return;
    }
    Panda_Debug::trace(false, debug_backtrace());
}

/**
 * リフレクション
 *
 * @param unknown_type $target
 * @param unknown_type $cehckParent
 *
 * @return void
 */
function r($target, $cehckParent = false)
{
    Panda_Debug::reflect($target, $cehckParent);
}

/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id$
 * @link      http://www.bear-project.net/
 */

/**
 * Debug print 'v' for CLI
 *
 * @param mixed  $values $values1, $values2 ...
 *
 * @return void
 */
function v($values = null)
{
    static $paramNum = 0;

    // be recursive
    $args = func_get_args();
    if (count($args) > 1) {
        foreach ($args as $arg) {
            p($arg);
        }
    }
    $trace = debug_backtrace();
    $i = ($trace[0]['file'] === __FILE__ ) ? 1 : 0;
    $file = $trace[$i]['file'];
    $line = $trace[$i]['line'];
    $includePath = explode(":", get_include_path());
    // remove if include_path exists
    foreach ($includePath as $var) {
        if ($var != '.') {
            $file = str_replace("{$var}/", '', $file);
        }
    }
    $method = (isset($trace[1]['class'])) ? " ({$trace[1]['class']}" . '::' . "{$trace[1]['function']})" : '';
    $fileArray = file($file, FILE_USE_INCLUDE_PATH);
    $p = trim($fileArray[$line - 1]);
    unset($fileArray);
    $funcName = __FUNCTION__;
    preg_match("/{$funcName}\((.+)[\s,\)]/is", $p, $matches);
    $varName = isset($matches[1]) ? $matches[1] : '';
    // for mulitple arg names
    $varNameArray = explode(',', $varName);
    if (count($varNameArray) === 1) {
        $paramNum = 0;
        $varName = $varNameArray[0];
    } else {
        $varName = $varNameArray[$paramNum];
        if ($paramNum === count($varNameArray) - 1) {
            var_dump($_ENV);
            $paramNum = 0;
        } else {
            $paramNum++;
        }
    }
    $label = "$varName in {$file} on line {$line}$method";
    if (strlen(serialize($values)) > 1000000) {
        $ouputMode = 'dump';
    }
    $label = (is_object($values)) ? ucwords(get_class($values)) . " $label" : $label;
    // if CLI
    if (PHP_SAPI === 'cli') {
        $colorOpenReverse = "\033[7;32m";
        $colorOpenBold = "\033[1;32m";
        $colorOpenPlain = "\033[0;32m";
        $colorClose = "\033[0m";
        echo $colorOpenReverse . "$varName" . $colorClose . " = ";
        var_dump($values);
        echo $colorOpenPlain . "in {$colorOpenBold}{$file}{$colorClose}{$colorOpenPlain} on line {$line}$method" . $colorClose . "\n";
        return;
    }
    $labelField = '<fieldset style="color:#4F5155; border:1px solid black;padding:2px;width:10px;">';
    $labelField .= '<legend style="color:black;font-size:9pt;font-weight:bold;font-family:Verdana,';
    $labelField .= 'Arial,,SunSans-Regular,sans-serif;">' . $label . '</legend>';
    if (class_exists('FB', false)) {
        $label = 'p() in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'];
        FB::group($label);
        FB::error($values);
        FB::groupEnd();
        return;
    }
    $pre = "<pre style=\"text-align: left;margin: 0px 0px 10px 0px; display: block; background: white; color: black; ";
    $pre .= "border: 1px solid #cccccc; padding: 5px; font-size: 12px; \">";
    if ($varName != FALSE) {
        $pre .= "<span style='color: #660000;'>" . $varName . '</span>';
    }
    $pre .= "<span style='color: #660000;'>" . htmlspecialchars($varName) . "</span>";
    $post = '&nbsp;&nbsp;' . "in <span style=\"color:gray\">{$file}</span> on line {$line}$method";
    echo $pre;
    var_dump($values);
    echo $post;
}
