<?php

/**
 * Panda
 *
 * PHP versions 5
 *
 * @category  Panda
 * @package   Panda_Debug
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id$
 * @link      http://api.Panda-project.net/Panda_Debug/Panda_Debug.html
 */

require_once 'vendors/debuglib.php';

/**
 * Panda Debug Class
 *
 * Utility class for debugging mode.
 *
 * // show variables
 * Panda_Debug::p($mixed);
 *
 * // show backtrace link
 * Panda_Debug::trace();
 *
 * </pre>
 *
 * @category  Panda
 * @package   Panda_Debug
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @link      n/a
 *
 */


class Panda_Debug
{

    /**
     * print_a formart output
     */
    const OUTPUT_PRINTA = 'printa';

    /**
     * var export format output
     */
    const OUTPUT_VAR = 'var';

    /**
     * var export format output
     */
    const OUTPUT_EXPORT = 'export';

    /**
     * syslog output
     */
    const OUTPUT_SYSLOG = 'syslog';

    /**
     * firephp output
     */
    const OUTPUT_FIRE = 'fire';

    /**
     * switch
     *
     * make falese in live mode
     */
    public static $enable = true;

    /**
     * new不可
     *
     * @ignore
     */
    private function __construct()
    {
    }

    /**
     * Debug output
     *
     * <code>
     * Panda_Debug::p
     * 出力モードを指定して変数を出力します
     * </code>
     *
     * @param mixed  $values    any values
     * @param string $ouputMode 'var' | 'export' | 'syslog' | 'fire' dafult is 'print_a'
     * @param array  $options   options
     *
     * @return void
     */
    public static function p($values = '', $ouputMode = null, array $options = array())
    {
        if (!self::$enable) {
            return;
        }
        if (isset($options['return']) && $options['return'] === true) {
            ob_start();
        }
        // Roならarrayに
        if (class_exists('BEAR_Ro', false) && $values instanceof BEAR_Ro) {
            $values = array('code' => $values->getCode(),
                'headers' => $values->header,
                'body' => $values->body,
                'links' => $values->links);
        }
        if ($ouputMode === null && is_scalar($values)) {
            $ouputMode = 'dump';
        }
        $trace = isset($options['trace']) ? $options['trace'] : debug_backtrace();
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        $includePath = explode(":", get_include_path());
        $method = (isset($trace[1]['class'])) ? " ({$trace[1]['class']}" . '::' . "{$trace[1]['function']})" : '';
        $fileArray = file($file, FILE_USE_INCLUDE_PATH);
        $p = trim($fileArray[$line - 1]);
        unset($fileArray);
        preg_match("/p\((.+)[\s,\)]/is", $p, $matches);
        $varName = isset($matches[1]) ? $matches[1] : '';
        $label = isset($options['label']) ? $options['label'] : "$varName in {$file} on line {$line}$method";
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
        if (Panda::isCliOutput()) {
            if (class_exists('FB', false)) {
                $ouputMode = 'fire';
            } else {
                $ouputMode = 'syslog';
            }
        }
        $labelField = '<fieldset style="color:#4F5155; border:1px solid black;padding:2px;width:10px;">';
        $labelField .= '<legend style="color:black;font-size:9pt;font-weight:bold;font-family:Verdana,';
        $labelField .= 'Arial,,SunSans-Regular,sans-serif;">' . $label . '</legend>';
        switch ($ouputMode) {
        case 'v' :
        case 'var' :
            if (class_exists('Var_Dump', false)) {
                Var_Dump::displayInit(array(
                    'display_mode' => 'HTML4_Text'));
                print $labelField;
                Var_Dump::display($values);
            } else {
                ob_start();
                var_export($values);
                $var = ob_get_clean();
                print $labelField;
                echo "<pre>" . $var . '</pre>';
            }
            print "</fieldset>";
            break;
        case 'd' :
        case 'dump' :
            $file = "<a style=\"color:gray; text-decoration:none;\" target=\"_blank\" href=/__panda/edit/?file=$file&line=$line>{$file}</a>";
            $dumpLabel = isset($options['label']) ? $options['label'] : "in <span style=\"color:gray\">{$file}</span> on line {$line}$method";
            echo self::dump($values, null, array('label' => $dumpLabel,
                'var_name' => $varName));
            break;
        case 'e' :
        case 'export' :
            echo "$labelField<pre>" . var_export($values, true);
            echo '</fieldset></pre>';
            break;
        case 'h' :
        case 'header' :
            header("x-panda-$label", print_r($values, true));
            break;
        case 's' :
        case 'syslog' :
            syslog(LOG_DEBUG, "label:$label" . print_r($values, true));
            break;
        case 'f' :
        case 'fire' :
            if (class_exists('FB', false)) {
                $label = isset($options['label']) ? $options['label'] : 'p() in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'];
                FB::group($label);
                FB::error($values);
                FB::groupEnd();
            }
            break;
        case null :
        case 'printa' :
        case 'print_a' :
        default :
            $options = "max_y:100;pickle:1;label:{$label}";
            print_a($values, $options);
        }
        if (isset($options['return']) && $options['return'] === true) {
            return ob_get_clean();
        }
    }

    /**
     * Show reflection
     *
     * Show reflection
     *
     * <code>
     * Panda_Debug::reflect('BEAR_Form');  // Class
     * Panda_Debug::reflect($obj);　       // Objecy
     * Panda_Debug::reflect('p');         // Function
     * </code>
     *
     * @param string $target      target
     * @param boll   $cehckParent check parent class
     *
     * @return void
     */
    public static function reflect($target, $cehckParent = false)
    {
        if (is_object($target)) {
            $target = get_class($target);
        }
        switch (true) {
        case function_exists($target) :
            $ref = new ReflectionFunction($target);
            $info['name'] = $ref->isInternal() ? 'The internal ' : 'The user-defined ';
            $info['name'] .= $targetName = $ref->getName();
            $info['declare in'] = $ref->getFileName() . ' lines ' . $ref->getStartLine() . ' to ' . $ref->getEndline();
            $info['Documentation'] = $ref->getDocComment();
            $statics = $ref->getStaticVariables();
            if ($statics) {
                $info['Static variables'] = $statics;
            }
            $type = 'function';
            break;
        case class_exists($target, false) :
            $ref = new ReflectionClass($target);
            $type = 'class';
            $info['name'] = $ref->isInternal() ? 'The internal ' : 'The user-defined ';
            $info['name'] .= $ref->isAbstract() ? ' abstract ' : '';
            $info['name'] .= $ref->isFinal() ? ' final ' : '';
            $info['name'] .= $ref->isInterface() ? 'interface ' : 'class ';
            $info['name'] .= $targetName = $ref->getName();
            $info['declare in'] = $ref->getFileName() . ' lines ' . $ref->getStartLine() . ' to ' . $ref->getEndline();
            $info['modifiers'] = Reflection::getModifierNames($ref->getModifiers());
            $info['Documentation'] = $ref->getDocComment();
            $info['Implements'] = $ref->getInterfaces();
            $info['Constants'] = $ref->getConstants();
            foreach ($ref->getProperties() as $prop) {
                // ReflectionProperty クラスのインスタンスを生成する
                $propRef = new ReflectionProperty($targetName, $prop->name);
                if ($propRef->isPublic()) {
                    $porps[] = $prop->name;
                }
            }
//            $info['Public Properties'] = $porps;
            foreach ($ref->getMethods() as $method) {
                $methodRef = new ReflectionMethod($targetName, $method->name);

                if ($methodRef->isPublic() || $method->isStatic()) {
                    $final = $method->isFinal() ? 'final ' : '';
                    $pubic = $method->isPublic() ? 'public ' : '';
                    $static = $method->isStatic() ? ' static ' : '';
                    $methods[] = sprintf("%s%s%s %s", $final, $pubic, $static, $method->name);
                }
            }
            $info['Public Methods'] = $methods;
            if ($ref->isInstantiable() && is_object($target)) {
                $info['isInstance ?'] = $ref->isInstance($target) ? 'yes' : 'no';
            }
            if ($parent) {
                $info['parent'] .= $ref->getParentClass();
            }
            break;
        default :
            $type = 'Invalid Object/Class';
            $targetName = $target;
            $info = null;
            break;
        }
        print_a($info, "show_objects:1;label: Reflection of {$type} '{$targetName}'");
    }

    /**
     * Print backtrace link
     *
     * <code>
     * Panda_Debug::trace();
     * </code>
     *
     * @param string $return         return string if true
     * @param array  $debugBackTrace array:trace data false:trace from here
     *
     * @return void
     */
    public static function trace($return = false, $debugBackTrace = false)
    {
        assert(is_bool($return));
        assert(is_bool($debugBackTrace) || is_array($debugBackTrace));
        $debugBackTrace = $debugBackTrace ? $debugBackTrace : debug_backtrace();
        $id = md5(print_r($debugBackTrace, true));
        foreach ($debugBackTrace as &$row) {
            if (!isset($row['file']) && isset($row['class'])) {
                $ref = new ReflectionMethod($row['class'], $row['function']);
                $row['file'] = $ref->getFileName();
                $row['line'] = $ref->getStartLine();
                $row['end'] = $ref->getEndLine();
            }
        }
        $pageLogPath = Panda::getTempDir() . "/trace-{$id}.log";
        file_put_contents($pageLogPath, serialize($debugBackTrace));
        $style = "padding:3px;margin:0px 4px;font-size:12px;color:white;background-color:black;font:arial white";
        $name = "{$debugBackTrace[0]['file']} in {$debugBackTrace[0]['line']}";
        $link = '<a href="/__panda/trace/?id=' . $id . '" title="' . $name . '" target="_panda_trace_' . $id . '" style="';
        $link .= $style . '">trace</a>';
        if ($return === true) {
            return $link;
        }
        echo $link;
    }

    /**
     * Get trace summary HTML string
     *
     * <code>
     * echo getTraceSummary(debug_backtrace()); //output trace summary
     * </code>
     *
     * @param array $debuBackTrace trace data
     *
     * @return void
     */
    public static function getTraceSummary($debuBackTrace = null)
    {
        $debuBackTrace = is_null($debuBackTrace) ? debug_backtrace() : $debuBackTrace;
        $traceLevels = array_keys($debuBackTrace);
        $i = 0;
        $traceSummary = '';
        foreach ($traceLevels as $level) {
            $trace = $debuBackTrace[$level];
            if (isset($trace['file'])) {
                $file = $trace['file'];
                $line = $trace['line'];
                $fileArray = file($file);
                $hitLine = $fileArray[$line - 1];
            } elseif (isset($trace['class']) && isset($trace['function'])) {
                $ref = new ReflectionMethod($trace['class'], $trace['function']);
                $file = $ref->getFileName();
                $line = $ref->getStartLine();
                $fileArray = file($file);
                $hitLine = $fileArray[$line - 1];
            } elseif (isset($trace['function'])) {
                $ref = new ReflectionFunction($trace['function']);
                $file = $ref->getFileName();
                $line = $ref->getStartLine();
                $fileArray =  ($file) ? file($file) : array();
                $hitLine = ($file) ? $fileArray[$line - 1] : '';
            } else {
                $file = false;
                $hitLine = 'n/a';
            }
            $hitLine = trim($hitLine);
            $args = array();
            if (isset($trace['args'])) {
                foreach ($trace['args'] as $arg) {
                    if (is_array($arg)) {
                        $args[] = 'Array';
                    } elseif (is_string($arg)) {
                        $args[] = "'{$arg}'";
                    } elseif (is_scalar($arg)) {
                        $args[] = $arg;
                    } else {
                        $args[] = 'Object';
                    }
                }
            }
            if (isset($trace['class'])) {
                $hitInfo = "{$trace['class']}{$trace['type']}{$trace['function']}({$args}) ";
            } elseif (isset($trace['function'])) {
                $hitInfo = "{$trace['function']}({$args}) ";
            } else {
                $hitInfo = '';
            }
            $args = implode(',', $args);
            $traceSummary .= '<li><span class="timeline-num">' . $i . '</span>';
            $traceSummary .= '<span class="timeline-body">' . $hitLine . '</span>';
            $traceSummary .= '<span class="timeline-info">' . $hitInfo . '<br />';
            $traceSummary .= $file ? Panda::getEditorLink($file, $line) : '';
            $traceSummary .= '</span></li>';
            $i++;
        }
        $traceSummary = '<ol id="trace-summary" class="timeline">' . $traceSummary . '</ol>';
        return $traceSummary;
    }

    /**
     * Return Var dump
     *
     * @param mixed  $var     variables
     * @param string $varName variable name
     * @param bool   $pInfo
     *
     * @return string
     *
     * @author highstrike at gmail dot com
     * @author Akihito Koriyama
     *
     * @link http://www.php.net/manual/ja/function.var-dump.php#80288
     *
     */
    public static function dump(&$var, $varName = false, $pInfo = false)
    {
        $scope = false;
        $prefix = 'unique';
        $suffix = 'value';
        $vals = $scope ? $scope : $GLOBALS;
        $old = $var;
        $var = $new = $prefix . rand() . $suffix;
        $vname = FALSE;
        foreach ($vals as $key => $val)
            if ($val === $new)
                $vname = $key;
        $var = $old;
        $pre = "<pre style=\"text-align: left;margin: 0px 0px 10px 0px; display: block; background: white; color: black; ";
        $pre .= "border: 1px solid #cccccc; padding: 5px; font-size: 12px; \">";
        if ($varName != FALSE) {
            $pre .= "<span style='color: #660000;'>" . $varName . '</span>';
        }
        $post = '';
        if ($pInfo) {
            $pre .= "<span style='color: #660000;'>" . htmlspecialchars($pInfo['var_name']) . "</span>";
            $post = '&nbsp;&nbsp;' . $pInfo['label'];
        }
       if ($varName !== FALSE) {
            $result = $pre . self::_doDump($var) . $post . '</pre>';
        } else {
            $result = $pre . $post . "</pre>";
        }
        return $result;
    }

    /**
     * Var Dump - Sub
     *
     * @author php at mikeboers dot com
     * @author Akihito Koriyama
     *
     * @link http://www.php.net/manual/ja/function.var-dump.php#76072
     * @ignore
     */
    private static function _doDump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
    {
        $do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
        $reference = $reference . $var_name;
        $keyvar = 'the_doDump_recursion_protection_scheme';
        $keyname = 'referenced_object_name';
        $result = '';
        if (is_array($var) && isset($var[$keyvar])) {
            $real_var = &$var[$keyvar];
            $real_name = &$var[$keyname];
            $type = (gettype($real_var));
            $result .= "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
        } else {
            $var = array($keyvar => $var, $keyname => $reference);
            $avar = &$var[$keyvar];
            $type = (gettype($avar));
            $type_color = '';
            if ($type == "string")
                $type_color = "<span style='color:green'>";
            elseif ($type == "integer")
                $type_color = "<span style='color:red'>";
            elseif ($type == "double") {
                $type_color = "<span style='color:#0099c5'>";
                $type = "float";
            } elseif ($type == "bool")
                $type_color = "<span style='color:#92008d'>";
            elseif ($type == "NULL")
                $type_color = "<span style='color:black'>";

            if (is_array($avar)) {
                $count = count($avar);
                $result .= "$indent" . ($var_name ? "$var_name => " : " = ") . "<span style='color:black'>$type</span><br>$indent(<br>";
                $keys = array_keys($avar);
                foreach ($keys as $name) {
                    $value = &$avar[$name];
                    // use @ to avoid Fatal error: Maximum function nesting level of '100' reached, aborting!
                    @$result .= self::_doDump($value, "['$name']", $indent . $do_dump_indent, $reference);
                }
                $result .= "$indent)<br>";
            } elseif (is_object($avar)) {
                $result .= "$indent$var_name = <span style='color:#a2a2a2'>$type</span><br>$indent(<br>";
                foreach ($avar as $name => $value)
                    $result .= self::_doDump($value, "$name", $indent . $do_dump_indent, $reference);
                $result .= "$indent)<br>";
            } elseif (is_int($avar))
                $result .= "$indent$var_name = <span style='color:#a2a2a2'>($type)</span> $type_color$avar</span><br>";
            elseif (is_string($avar))
                $result .= "$indent$var_name = <span style='color:#a2a2a2'>($type)</span> $type_color'" . htmlspecialchars($avar) . "'</span><br>";
            elseif (is_float($avar))
                $result .= "$indent$var_name = <span style='color:#a2a2a2'>($type)</span> $type_color$avar</span><br>";
            elseif (is_bool($avar))
                $result .= "$indent$var_name = <span style='color:#a2a2a2'>($type)</span> $type_color" . ($avar == 1 ? "<span style='color:green'>TRUE</span>" : "<span style='color:red'>FALSE</span>") . "</span><br>";
            elseif (is_null($avar))
                $result .= "$indent$var_name = <span style='color:#a2a2a2'>($type)</span> {$type_color}NULL</span><br>";
            else
                $result .= "$indent$var_name = <span style='color:#a2a2a2'>($type)</span> $avar<br>";

            $var = $var[$keyvar];
        }
        return $result;
    }
}