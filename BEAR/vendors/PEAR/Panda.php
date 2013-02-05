<?php

/**
 * Panda
 *
 * PHP versions 5
 *
 * @category  Panda
 * @package   Panda
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2009 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: v.0.1.0 $Id: Panda.php 97 2009-10-08 03:45:58Z koriyama@users.sourceforge.jp $
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

// for 5.2.x or earlier
if (!defined('E_DEPRECATED')) {
    define('E_DEPRECATED', 8192);
    define('E_USER_DEPRECATED', 16384);
}

/**
 * Panda Class
 *
 * <pre>
 * * enhance PHP standard error more powerful.
 * * debug output utility
 * * standard HTTP code output
 * </pre>
 *
 * Example 1. HTTP Code error output.
 * </pre>
 * <code>
 * throw new Panda_Exception('Sorry, The Item is not available now.', 404, $debugInfo);
 * </code>
 *
 * @category  Panda
 * @package   Panda
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Panda.php 97 2009-10-08 03:45:58Z koriyama@users.sourceforge.jp $
 * @link      http://api.Panda-project.net/Panda/Panda.html
 */
class Panda
{
    /**
     * Version
     */
    const VERSION =  '0.3.39';

    /**
     * Package name - App
     */
    const PACKAGE_APP = 0;

    /**
     * Package name - PHP
     */
    const PACKAGE_PHP = 1;

    /**
     * Package name - Assert
     *
     * @var int
     */
    const PACKAGE_ASSERT = 2;

    /**
     * Package name - PEAR
     *
     * @var int
     */
    const PACKAGE_PEAR = 3;

    /**
     * Package name - Exception
     *
     * @var string
     */
    const PACKAGE_EXECEPTION = 4;

    /**
     * config debug mode
     *
     * @var string
     */
    const CONFIG_DEBUG = 'debug';

    /**
     * config report project path
     *
     * @var string
     */
    const CONFIG_VALID_PATH = 'valid';

    /**
     * config log path
     *
     * @var string
     */
    const CONFIG_LOG_PATH = 'log_path';

    /**
     * config callback on fire
     *
     * @var string
     */
    const CONFIG_ON_ERROR_FIRED = 'on_error';

    /**
     *  config callback on shutdown script (for logging)
     *
     * @var string
     */
    const CONFIG_ON_ERROR_END = 'on_end';

    /**
     * config fatal error callback
     *
     * @var string
     */
    const CONFIG_ON_FATAL_ERROR = 'on_fatal_error';

    /**
     * config - use firephp ?
     *
     * @var string
     */
    const CONFIG_ENABLE_FIREPHP = 'firephp';

    /**
     * config - fatal error template
     *
     * @var string
     */
    const CONFIG_FATAL_HTML = 'fatal_html';

    /**
     * config  - Http503 template
     *
     * @var string
     */
    const CONFIG_HTTP_TPL = 'http_tpl';

    /**
     * config catch fatal error ?
     *
     * @var string
     */
    const CONFIG_CATCH_FATAL = 'catch_fatal';

    /**
     * config catch faatal strict error ?
     *
     * @var string
     */
    const CONFIG_CATCH_STRICT = 'catch_strict';

    /**
     * config for growl
     *
     * @var string
     */
    const CONFIG_GROWL = 'growl';

    /**
     * Panda::error options - severity
     *
     * @var string
     */
    const ERROR_OPTION_FILE = 'file';

    /**
     * Panda::error options - severity
     *
     * @var string
     */
    const ERROR_OPTION_LINE = 'line';

    /**
     * Panda::error options - severity
     *
     * @var string
     */
    const ERROR_OPTION_TRACE = 'trace';

    /**
     * Panda::error options - severity
     *
     * @var string
     */
    const ERROR_OPTION_RETRUN = 'return';

    /**
     * Panda::error options - severity
     *
     * @var string
     */
    const ERROR_OPTION_NO_SCREEN = 'no_screen';

    /**
     * Panda htdocs path
     */
    const CONFIG_PANDA_PATH = 'path';


    /**
     * Config - Editor
     *
     * @var string
     */
    const CONFIG_EDITOR = 'edit';

    /**
     * Editor - Textmate
     *
     * @var int
     */
    const EDITOR_TEXTMATE = 1;

    /**
     * Editor - Bespin
     *
     * @var int
     */
    const EDITOR_BESPIN = 2;

    /**
     * Error name data
     *
     * @static array
     */
    static $phpError = array(
    E_ERROR => 'E_ERROR',
    E_WARNING => 'E_WARNING',
    E_PARSE => 'E_PARSE',
    E_NOTICE => 'E_NOTICE',
    E_CORE_ERROR => 'E_CORE_ERROR',
    E_CORE_WARNING => 'E_CORE_WARNING',
    E_COMPILE_ERROR => 'E_COMPILE_ERROR',
    E_COMPILE_WARNING => 'E_COMPILE_WARNING',
    E_USER_ERROR => 'E_USER_ERROR',
    E_USER_WARNING => 'E_USER_WARNING',
    E_USER_NOTICE => 'E_USER_NOTICE',
    E_STRICT => 'E_STRICT',
    E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
    E_DEPRECATED => 'E_DEPRECATED',
    E_USER_DEPRECATED => 'E_USER_DEPRECATED'
    );

    /**
     * PHP Error code statics
     *
     * @var integer
     *
     */
    private static $_errorStat = 0;

    /**
     * Errors in outer path
     *
     * @var array
     */
    private static $_outerPathErrors = array();

    /**
     * Errors in all path
     *
     * @var array
     */
    private static $_errors = array();

    /**
     * Default configration
     *
     * @var array
     */
    private static $_config = array(
        self::CONFIG_DEBUG => false,
        self::CONFIG_VALID_PATH => array('/'),
        self::CONFIG_LOG_PATH => '/tmp',
        self::CONFIG_ON_ERROR_FIRED => false,
        self::CONFIG_ON_FATAL_ERROR => false,
        self::CONFIG_ENABLE_FIREPHP => true,
        self::CONFIG_FATAL_HTML => 'Panda/template/fatal.php',
        self::CONFIG_HTTP_TPL => 'Panda/template/http.php',
        self::CONFIG_CATCH_FATAL => false,
        self::CONFIG_CATCH_STRICT => true,
        self::CONFIG_PANDA_PATH => '/',
        self::CONFIG_EDITOR => 0,
        self::CONFIG_GROWL => false,
    );

    /**
     * Get config
     *
     * @return array
     */
    public static function getConfig()
    {
        return self::$_config;
    }

    /**
     * Init
     *
     * reset error/exception handler and atach panda handler
     *
     * @param array $config config
     *
     * @return void
     */
    public static function init(array $config = array())
    {
        if (isset($_GET['_nopanda'])) {
            return;
        }
        self::$_config = array_merge(self::$_config, $config);
        // reset handler
        if (self::$_config[self::CONFIG_DEBUG] !== true) {
            ini_set('display_errors', 0);
            function p($v = ''){
                syslog(LOG_INFO, print_r($v, true));
            }
            if (class_exists('PEAR', false)) {
                PEAR::setErrorHandling(PEAR_Exception);
            }
            set_exception_handler(array('Panda', 'onException'));
        } else {
            if (self::$_config[self::CONFIG_ENABLE_FIREPHP]) {
                include_once 'FirePHPCore/FirePHP.class.php';
                include_once 'FirePHPCore/fb.php';
            }
            self::_initOnDebug();
            if (self::$_config[self::CONFIG_CATCH_FATAL] === true) {
                ob_start(array('Panda', 'onFatalError'));
            }
            // catch E_STRICT
            if (self::$_config[self::CONFIG_CATCH_STRICT] === true) {
                error_reporting(E_ALL | E_STRICT);
                ob_start(array('Panda', 'onStrictError'));
            }
        }
    }

    /**
     * Init in Debug mode
     *
     * @return void
     */
    private static function _initOnDebug()
    {
        require_once 'Panda/Debug.php';
        require_once 'Panda/Debug/util.php';
        require_once 'Panda/Exception.php';
//        include_once 'Net/Growl.php';
        ini_set('display_errors', 1);
        // アサーションを有効
        assert_options(ASSERT_ACTIVE, 1);
        assert_options(ASSERT_WARNING, 0);
        assert_options(ASSERT_QUIET_EVAL, 1);
        assert_options(ASSERT_CALLBACK, (array('Panda', 'onAssert')));
        restore_error_handler();
        restore_exception_handler();
        set_exception_handler(array('Panda', 'onException'));
        if (class_exists('PEAR', false)) {
            PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('Panda', 'onPearError'));
        }
        if (class_exists('PEAR_ErrorStack', false)) {
            PEAR_ErrorStack::setDefaultCallback(array('Panda', 'onStackError'));
        }
        set_error_handler(array('Panda', 'onDebugPhpError'));
    }

    /**
     * Resotre Panda errpr handler
     *
     * <pre>
     * restore Panda error handler after 'restore_error_handler' or 'restore_exception_handler'
     *
     * (Note) Error in reflection 'invoke' method seems unstable,
     * I think its better detache handler before invoke then call this method again.
     * </pre>
     *
     * @returnv void
     *
     */
    public static function restoreHandler()
    {
        set_error_handler(array('Panda', 'onDebugPhpError'));
        set_exception_handler(array('Panda', 'onException'));
    }

    /**
     * Assert Handler
     *
     * @param string  $file file
     * @param integer $line line
     * @param integer $code code
     *
     * @return void
     */
    public static function onAssert($file, $line, $code)
    {
        $trace = debug_backtrace();
        array_shift($trace);
        $options['file'] = $trace[0]['file'];
        $options['line'] = $trace[0]['line'];
        $options['trace'] = $trace;
        $options['package'] = self::PACKAGE_ASSERT;
        self::error('Assert Error', $code, null, $options);
    }

    /**
     * PEAR::ErrorStack Handler
     *
     * @param PEAR_Error $error PEAR Error object
     *
     * @return mixed
     */
    public static function onStackError($error)
    {
        return PEAR_ERRORSTACK_PUSH;
    }

    /**
     * PEAR error handler
     *
     * @param object $error PEAR Error object
     *
     * @return void
     * @ignore
     */
    public static function onPearError(PEAR_Error $error)
    {
        $trace = debug_backtrace();
        array_shift($trace);
        array_shift($trace);
        array_shift($trace);
        $options['file'] = $trace[0]['file'];
        $options['line'] = $trace[0]['line'];
        $options['trace'] = $trace;
        $options['package'] = self::PACKAGE_PEAR;
        $debugInfo = $error->getDebugInfo();
        $userInfo = $error->getUserInfo();
        $trace = ($error->getBackTrace());
        $info = array('Error Type' => $error->getType(),
            'Debug Info' => $error->getUserInfo());
        if ($debugInfo !== $userInfo) {
            $info['User Info'] = $userInfo;
        }
        if (self::$_config[self::CONFIG_DEBUG] === true) {
            self::error('PEAR Error', $error->getCode() . ' ' . $error->getMessage(), $info, $options);
        } else {
            error_log('PEAR Error' .  $error->getCode() . ' ' . $error->getMessage(). " in file[{$options['file']}] on line [{$options['line']}", 0);
        }
    }

    /**
     * PHP error handler in debug mode
     *
     * @param int    $code       code
     * @param string $message    message
     * @param string $file       file
     * @param int    $line       line
     * @param mixed  $errcontext error context (local variables)
     *
     * @return object
     */
    public static function onDebugPhpError($code, $message, $file, $line, array $errcontext)
    {
        $type = self::$phpError[$code];
        $simpleErrorString =  "[{$type}] {$message} in {$file} on line {$line}";
        if ($code !== E_STRICT) {
            // apache log all error expect E_STRICT even @ symbol
            error_log($simpleErrorString, 0);
        }
        // @?
        if (error_reporting() === 0) {
            return;
        }
        /**
         * to avoid repeat reported error
         */
        static $_reported = array();

        static $_cnt = 0;

        // me ?        // me ?
        if ($file === __FILE__) {
            $msg = "<b>Error in Panda ! PHP Error [$simpleErrorString] captured in [" . __FILE__ ." on line ". __LINE__;
            throw new Exception($msg);
        }
        if($_cnt++ > 100) {
            return;
        }
        self::$_errorStat |= $code;
        // valid path ?
        if (self::isValidPath($file) !== true) {
            self::$_outerPathErrors[$code] = $simpleErrorString;
            self::$_errors[$code] = $simpleErrorString;
            if ($code & E_NOTICE || $code & E_STRICT || $code & E_WARNING) {
                return;
            }
        }
        $errorString = self::$phpError[$code];

        // igore repeated error (same as ignore_repeated_errors 0)
        $key = "{$message}{$file}{$line}";
        if (isset($_reported[$key])) {
            $_reported[$key]++;
            return;
        } else {
            $_reported[$key] = 1;
        }
        $trace = debug_backtrace();
        array_shift($trace);
        $options['file'] = $file;
        $options['line'] = $line;
        $options['trace'] = $trace;
        // hide weak error
        $noScreen = ($code & (E_NOTICE | E_STRICT | E_DEPRECATED)) ? true : false;
        $noErrorLog =  ($code & (E_STRICT)) ? true : false;
        if (!$noErrorLog) {
            error_log($simpleErrorString);
        }
        $options['no_screen'] = $noScreen;
        $options['severity'] = $code;
        // E_STRICT tweak
        $matches = array();
        if ($code == E_STRICT) {
            preg_match('/Non-static method (.*)::(.*)\(\)/', $message, $matches);
            if ($matches) {
                $class = $matches[1];
                assert(class_exists($class, false));
                $ref = new ReflectionClass($class);
                $file = $ref->getFileName();
                if (!self::isValidPath($file)) {
                    return;
                }
            }
        }
        $options['package'] = self::PACKAGE_PHP;
        self::error($errorString, $message, $errcontext, $options);
    }

    /**
     * PHP error handler in live mode
     *
     * @param int    $code       code
     * @param string $message    message
     * @param string $file       file
     * @param int    $line       line
     * @param mixed  $errcontext error context (local variables)
     *
     * @return void
     */
    public static function onLivePhpError($code, $message, $file, $line, array $errcontext)
    {
        $trace = debug_backtrace();
        array_shift($trace);
        $options['file'] = $file;
        $options['line'] = $line;
        $options['trace'] = $trace;
        $errorString = self::$phpError[$code];
        $options['package'] = self::PACKAGE_PEAR;
        self::error($errorString, $message, $errcontext, $options);
    }

    /**
     * Uncaught exception handler
     *
     * <pre>
     * All uncaught exception but Panda_Exception produce HTTP 503(Service Temporary Unavailable) Error.
     * Crawler may not crawl by this code. (and come back later)
     * </pre>
     *
     * @param Exception $e Panda_Exception | any Exception
     *
     * @return void
     */
    public static function onException(Exception $e, $httpScreenOutput = true)
    {
        try {
            $class = get_class($e);
            $body = null;
            $info = array();
            if ($e instanceof Panda_End_Exception) {
                exit();
            } elseif ($e instanceof Panda_Exception) {
                $httpCode = $e->getCode();
                $body = $e->getMessage();
            } elseif ($e instanceof ErrorException) {
                $info['severity'] = $e->getSeverity();
                $httpCode = 503;
            } else {
                $info['code'] = $e->getCode();
                $httpCode = 503;
            }
            $info['info'] = (method_exists($e, 'getInfo')) ? $e->getInfo() : null;
            $id = 'e' . $e->getLine() . '-' .substr(md5($e->getFile() . $e->getCode() . $e->getCode()), 0, 6);
            if ($httpScreenOutput === true) {
                self::outputHttpStatus($httpCode, true, $body, $id);
            }
            $options = array('file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace());
            $options['package'] = self::PACKAGE_EXECEPTION;
            $code = $e->getCode();
            $msg = $e->getMessage();
            $infoStr =  (method_exists($e, 'getInfo')) ? print_r($e->getInfo(), true) : null;
            $trace = $e->getTraceAsString();
            $class = get_class($e);
            $log = "[$code] $class - $msg - $infoStr";
            error_log($log);
            if (isset(self::$_config[self::CONFIG_LOG_PATH])) {
                $filePath = self::$_config[self::CONFIG_LOG_PATH] . "$id.log";
                if (!file_exists($filePath)) {
                    @file_put_contents($filePath, "$log\n[Info]:\n" . print_r($info, true) . "\n[Trace]" . print_r($e->getTrace(), true));
                }
            }
            if (self::$_config['debug'] === true) {
                self::error(get_class($e), $e->getCode() . ' ' . $e->getMessage(), $info, $options);
            }
        } catch(Exception $e) {
            $bug = get_class($e) . " thrown within the exception handler. Message: " . $e->getMessage() . " on line " . $e->getLine();
            self::_debugPrint($bug);
        }
    }

    /**
     * Check if $file is under the path.
     *
     * @param string $file file path
     *
     * @return bool
     */
    public static function isValidPath($file)
    {
        $isValidPath = false;
        foreach (self::$_config[self::CONFIG_VALID_PATH] as $validPath) {
            if (strpos($file, $validPath) === 0) {
                $isValidPath = true;
                break;
            }
        }
        if ($isValidPath === false) {
            //            $isValidPath = true;
        }
        return $isValidPath;
    }

    /**
     * Read css string
     *
     * @param string $headerColor header color
     *
     * @return string
     */
    private static function _getDebugCss($headerColor = 'red')
    {
        static $_done = false;
        if ($_done) {
            return '';
        }
        $_done = true;
        $cssPanda = file_get_contents('Panda/templates/debug.css', FILE_USE_INCLUDE_PATH);
        $cssPanda = str_replace('{header_color}', $headerColor, $cssPanda);
        $result = '';
        if (false && class_exists('DbugL', false) && method_exists('DbugL', 'html_prefix')) {
            $cssPrinta = DbugL::html_prefix();
            $result .= str_replace("\n", '', $cssPrinta);
        }
        $result .= '<style type="text/css"><!--' . trim(str_replace("\n", '', $cssPanda));
        $result .= ' --></style>';
        return $result;
    }

    /**
     * Is CLI Output ?
     *
     * <pre>
     * Check if CLI, Ajax or REST accesss.Logic can be injected by config.
     * </pre>
     *
     * @return bool
     */
    public static function isCliOutput()
    {
        return (PHP_SAPI === 'cli');
    }

    /**
     * Display Error Message in debug mode.
     *
     * <pre>
     *
     * @param string $heading    Headding
     * @param string $subheading Sub headding
     * @param mixed  $info       error info
     * @param array  $options    options
     *
     * $options key:
     *
     * string $options['file']   file name
     * int    $options['line']   line
     * array  $options['trace']  backtrace
     * bool   $options['return'] return string(no echo)
     * </pre>
     *
     * @return void
     */
    public static function error($heading, $subheading = "", $info = "", array $options = array())
    {
        // return if live
        if (self::$_config[self::CONFIG_DEBUG] !== true) {
            return;
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            if (class_exists('FB', false)) {
                $in = isset($options['file']) ? "- in {$options['file']} on line {$options['line']}" : '';
                $msg = "$heading - {$subheading} $in";
                if ($info) {
                    FB::group($msg);
                    FB::error($info);
                    FB::groupEnd();
                    return;
                }
            }
        }
        static $num = 1; //div id number
        $heading = (is_numeric($heading)) ? self::$packageName[$heading] : $heading;
        // Application error callback
        if (is_callable(self::$_config[self::CONFIG_ON_ERROR_FIRED])) {
            call_user_func(self::$_config[self::CONFIG_ON_ERROR_FIRED], $heading, $subheading, $info, $options);
        }
        $fileInfoString = isset($options['file']) ? "in {$options['file']} on line {$options['line']}" : 'in unknown file';
        if (self::$_config[self::CONFIG_ENABLE_FIREPHP] && isset($options['severity'])) {
            $fireLevel = FirePHP::ERROR;
            switch (true) {
                case ($options['severity'] == E_WARNING || $options['severity'] == E_USER_WARNING) :
                    $fireLevel = FirePHP::WARN;
                    break;
                case ($options['severity'] == E_NOTICE || $options['severity'] == E_USER_NOTICE) :
                    $fireLevel = FirePHP::INFO;
                    break;
                case ($options['severity'] == E_STRICT) :
                    $fireLevel = FirePHP::LOG;
                    break;
                default :
                    $fireLevel = FirePHP::ERROR;
                break;
            }
            FB::send("{$subheading} - {$fileInfoString}", $heading, $fireLevel);
        }
        self::GrowlNotify($heading, $subheading . "\n{$fileInfoString}");
        $defaultOptions = array('file' => null,
            'line' => null,
            'trace' => array(),
            'return' => false,
            'no_screen' => false);
        $options = array_merge($defaultOptions, $options);
        if ($options['no_screen']) {
            return '';
        }
        $output = self::_getDebugCss();
        if ($options['trace']) {
            $traceId = uniqid();
            $traceFile = self::getTempDir() . '/trace-' . $traceId . '.log';
            $refData = self::__addReflectInfo($options['trace']);
            file_put_contents($traceFile, serialize($options['trace']));
            file_put_contents("{$traceFile}.ref.log", serialize($refData));
        }
        if (PHP_SAPI == 'cli' || self::isCliOutput()) {
            $output .= $heading . PHP_EOL;
            $output .= $subheading . PHP_EOL;
            $output .= $info . PHP_EOL;
            return $output;
        } else {
            if (is_array($info) || is_object($info)) {
                if (isset($options['package']) && ($options['package'] === self::PACKAGE_PHP)) {
                    $info = self::_getContextString($info, '$');
                } else {
                    $info = self::_getContextString($info);
                }
            }
            header('Content-Type: text/html; charset=utf-8');
            $output .= '<div id="panda-' . $num . '"class="panda">';
            $output .= '<div class="panda-header">';
            $output .= '<h1>' . $heading . '</h1>';
            $output .= isset($options['file']) ? '<p class="panda-file">' . self::getEditorLink($options['file'], $options['line'], 0) . '</p>' : '';
            $output .= '<h2>' . $subheading . '</h2>';
            $output .= '<p class="panda-cmd">';
            if ($options['trace']) {
                $output .= '<a target="_panda_trace_' . $traceId . '" href="' . "http://{$_SERVER['SERVER_NAME']}" . self::$_config[self::CONFIG_PANDA_PATH] . '__panda/trace/?id=' . $traceId . '">trace</a> | ';
            }
            $output .= '<a href=# onclick="var t = document.getElementById(\'panda-';
            $output .= $num . '\'); t.parentNode.removeChild(t);return false;">close</a>';
            $output .= '</p>';
            $output .= '</div>';
            $output .= '<div class="panda-body">';
            $output .= $info ? '<h3>Info</h3><p class="panda-info">' . $info . '</p>' : '';
            // file summary
            if ($options['file']) {
                $output .= '<h3>Source</h3>';
                $output .= '<a style="text-decoration:none;" href="/__panda/edit/?file=' . $options['file'];
                $output .= '&line=' . $options['line'] . '">';
                $output .= self::_getFileSummary($options['file'], $options['line'], 6);
                $output .= '</a>';
                if ($options['trace']) {
                    // trace summary
                    $output .= '<h3>Trace</h3>';
                    $output .= Panda_Debug::getTraceSummary($options['trace']);
                }
            }
            $output .= '</div></div>'; // /body-error-body /panda
        }
        $num++;
        if ($options['return'] === true) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Growl notify
     *
     * This needs.
     *
     * 1) Growl installation.
     * 2) Growl setting for remote application acception.
     *
     * @param string $title
     * @param string $description
     */
    public static function growlNotify($title, $description)
    {
        static $growlApp;

        if (self::CONFIG_GROWL !== true) {
            return;
        }

        if (!$growlApp) {
            $growlApp = new Net_Growl_Application('Panda', array('Panda_Growl_Notify'));
        }
        $growl = Net_Growl::singleton($growlApp, null, null);
        $growl->setNotificationLimit(16);
        $result = $growl->notify('Panda_Growl_Notify', $title, $description);
    }

    /**
     * Message Output
     *
     * @param string  $heading
     * @param message $subheading
     * @param array   $info
     *
     * @return void
     */
    public static function message($heading, $subheading = '', $info = '')
    {
        static $num = 1; //div id number

        $output = self::_getDebugCss('green');
        $output .= '<div id="panda-' . $num . '"class="panda">';
        $output .= '<div class="panda-header">';
        $output .= '<h1>' . $heading . '</h1>';
        $output .= '<h2>' . $subheading . '</h2>';
        $output .= '<p class="panda-cmd">';
        $output .= '<a href=# onclick="var t = document.getElementById(\'panda-';
        $output .= $num . '\'); t.parentNode.removeChild(t);return false;">close</a>';
        $output .= '</p>';
        $output .= '</div>';
        if ($info) {
            $output .= '<div class="panda-body"><p class="panda-info">' . $info . '</p>';
        }
        $output .= '</div></div>'; // /body-error-body /panda
        header('Content-Type: text/html; charset=utf-8');
        echo $output;
    }

    private static function _getContextString($context, $prefix = '')
    {
        $result = '';
        foreach ($context as $varName => $varVal) {
            if (class_exists('Panda_Debug')) {
                $result .= Panda_Debug::dump($varVal, $prefix . $varName);
            } else {
                $result .= $varName . ' = ' . print_r($varVal, true);
            }
        }
        return $result;
    }

    /**
     * Add reflection info
     *
     * @param array $trace
     *
     * @return string
     */
    private static function __addReflectInfo($trace)
    {
        $refLog = array();
        $i = 0;
        foreach ($trace as $row) {
            if (isset($row['class']) && isset($row['function'])) {
                if (isset($row['object'])) {
                    $refLog[$i]['export'] = ReflectionObject::export($row['object'], true);
                }
                $ref = new ReflectionMethod($row['class'], $row['function']);
                $refLog[$i]['file'] = $ref->getFileName();
                $refLog[$i]['doc'] = $ref->getDocComment();
                $refLog[$i]['start'] = $ref->getStartLine();
                $refLog[$i]['end'] = $ref->getEndLine();
            }
            $i++;
        }
        return $refLog;
    }

    /**
     * File summery
     *
     * @param string $file file name
     * @param int    $line line
     * @param int    $num  show line/2
     *
     * @return string
     */
    static function _getFileSummary($file, $line, $num = 6)
    {
        if (!file_exists($file)) {
            return ;
        }
        $result = '<div class="file-summary">';
        $files = file($file);
        $fileArray = array_map('htmlspecialchars', $files);
        $hitLineOriginal = $fileArray[$line - 1];
        $fileArray[$line - 1] = "<span class=\"hit-line\">{$fileArray[$line - 1]}</span>";
        $shortListArray = array_slice($fileArray, $line - $num, $num * 2);
        $shortListArray[$num - 1] = '<strog>' . $fileArray[$line - 1] . '</strong>';
        $shortList = implode('', $shortListArray);
        $shortList = '<pre class="short-list" style="background-color: #F0F0F9;">' . $shortList . '</pre>';
        $hitLine = $fileArray[$line - 1];
        $result .= $shortList . '</div>';
        return $result;
    }


    /**
     * File summery
     *
     * @param string $file file name
     * @param int    $line line
     * @param int    $num  show line/2
     *
     * @return string
     */
    static function _getFileSummary2($file, $line, $num = 6)
    {
        static $editorNum = 0;
        static $oneTimeSetUp = false;

        if (!file_exists($file)) {
            return ;
        }
        if ($oneTimeSetUp === true) {
            return;
        }
        $oneTimeSetUp = true;
        $result = <<<EOD
<script src="/__panda/edit/ace/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="/__panda/edit/ace/mode-php.js" type="text/javascript" charset="utf-8"></script>
<div id="editor">
EOD;
        $result .= htmlspecialchars(file_get_contents($file));
        $result .= <<<EOD
</div>
<script type="text/javascript">
window.onload = function() {
	var editor = ace.edit("editor",{
		//initialContent:'hello world'
	});
	var mode = require("ace/mode/php").Mode;
	editor.getSession().setMode(new mode());
};
EOD;
        $result .= '</script>';
        return $result;
    }
    /**
     *  Return file link with text editer.
     *
     * @param string $file    file name
     * @param int    $line    line
     * @param int    $column  column
     * @param string $content link string content
     *
     * @return string
     */
    public static function getEditorLink($file, $line = 0, $column = 0, $content = false)
    {
        $content = ($content) ? $content : "$file on line $line";
        if (self::$_config[self::CONFIG_EDITOR] & self::EDITOR_TEXTMATE) {
            $textmate = '<a title="Edit with TextMate" border="0" href="txmt://open/?url=file://';
            $textmate .= $file . '&line=' . $line . '&column=' . $column . '">' . '<img src="/__panda/image/textmate.png" border="0" >' . '</a>';
        } else {
            $textmate = '';
        }
        if (self::$_config[self::CONFIG_EDITOR] & self::EDITOR_BESPIN) {
            $bespin = '<a title="Online edit" border="0" href="/__panda/edit/?file=';
            $bespin .= $file . '&line=' . $line . '&column=' . $column . '">' . '<img src="/__panda/image/bespin.png" border="0" >' . '</a>';
        } else {
            $bespin = '';
        }
        return $content . $textmate . $bespin;
    }

    /**
     * Fatal Error Handler
     *
     * @param string $buffer PHP output buffer
     *
     * @return string
     * @ignore
     *
     * <code>
     * ob_start(array('Panda_Errpr', 'fatalErrorHandler'); // set handler
     * </code>
     */
    public static function onFatalError($buffer)
    {
        $error = error_get_last();
       // return "FATAL" . $buffer . "FATAL" . var_export($error, true);
        if($error['type'] !== 1){
            return $buffer;
        }
        // Web
        header("HTTP/1.x 503 Service Temporarily Unavailable.");
        $id = substr(md5(serialize($error)), 0, 6);
        // FB
        if (self::$_config[self::CONFIG_DEBUG] === true && self::$_config[self::CONFIG_ENABLE_FIREPHP]) {
            FB::error("Fatal Error - {$error['message']} - ref# {$id}");
        }
        // write fatal error in file
        if (self::$_config[self::CONFIG_LOG_PATH]) {
            $path = self::$_config[self::CONFIG_LOG_PATH];
            $msg = "{$error['message']} in {$error['line']} on {$error['line']}";
            error_log("[PHP Fatal][{$id}]:{$msg}");
            if (!is_writable($path)) {
                trigger_error('File write error' . $path　, E_USER_ERROR);
            }
            $file = $path . 'fatal-' . $id . '.log';
            if (!file_exists($file)) {
                file_put_contents($file, $buffer);
            } else {
            }
        }
        // show Fatal error page
        $fatalHtml = include self::$_config[self::CONFIG_FATAL_HTML];
        if (is_string($fatalHtml)) {
            return $fatalHtml;
        }
        return $buffer;
    }

    /**
     * Catch strict error in screen
     *
     * <pre>
     * catch strict error in compile from screen output.(error handler can't catch it)
     * </pre>
     *
     * @param string $buffer Output buffer
     *
     * @return string Output buffer
     */
    public static function onStrictError($buffer)
    {
        $matches = array();
        $regex = '/Strict Standards: (.*)( in )(.*)( on line )(\d+)/';
        preg_match_all($regex, $buffer, $matches, PREG_SET_ORDER);
        $buffer = preg_replace($regex, '', $buffer);
        if (!$matches) {
            return $buffer;
        }
        foreach ($matches as $match) {
            $error = array('all' => $match[0],
                'message' => $match[1],
                'file' => $match[3],
                'line' => $match[5]);
            $options = $error;
            $options['no_screen'] = true;
            $options['severity'] = E_STRICT;
            if (self::isValidPath($error['file']) === true) {
                self::error('E_STRICT', $error['message'], null, $options);
            }
        }
        return $buffer;
    }

    /**
     * HTTP Code output
     *
     *
     * @param int    $code     HTTP status code
     * @param bool   $withBody is with body ?
     * @param string $body     http body
     *
     * @return void
     */
    public static function outputHttpStatus($code, $withBody = true, $body = '', $id='')
    {
        assert(is_numeric($code));
        $codeMsgTable = array('200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '203' => 'Non-Authoritative Information',
            '204' => 'No Content',
            '205' => 'Reset Content',
            '206' => 'Partial Content',
            '300' => 'Multiple Choices',
            '301' => 'Moved Permanently',
            '302' => 'Found',
            '304' => 'Not Modified',
            '305' => 'Use Proxy',
            '307' => 'Temporary Redirect',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '406' => 'Not Acceptable',
            '407' => 'Proxy Authentication Required',
            '408' => 'Request Timeout',
            '409' => 'Conflict',
            '410' => 'Gone',
            '411' => 'Length Required',
            '412' => 'Precondition Failed',
            '413' => 'Request Entity Too Large',
            '414' => 'Request-URI Too Long',
            '415' => 'Unsupported Media Type',
            '416' => 'Requested Range Not Satisfiable',
            '417' => 'Expectation Failed',
            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable',
            '504' => 'Gateway Timeout',
            '505' => 'HTTP Version Not Supported');
        if (isset($codeMsgTable[$code])) {
            $codeMsg = $codeMsgTable[$code];
        } else {
            $codeMsg = '';
        }
        $serverProtocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : false;
        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            header("Status: {$code} {$codeMsg}", true);
        } elseif ($serverProtocol == 'HTTP/1.1' or $serverProtocol == 'HTTP/1.0') {
            header($serverProtocol . " {$code} {$codeMsg}", true, $code);
        } else {
            header("HTTP/1.1 {$code} {$codeMsg}", true, $code);
        }
        if (!$withBody) {
            return;
        }
        if (!$serverProtocol || self::isCliOutput()) {
            echo "{$code} {$codeMsg}" . PHP_EOL . PHP_EOL;
        } else {
            $error = array('color' => '#FF8C00',
                'code' => $code,
                'codeMsg' => $codeMsg,
                'body' => $body,
                'serverProtocol' => $serverProtocol,
                'id' => $id);
            $include = include self::$_config[self::CONFIG_HTTP_TPL];
            if (!$include) {
                trigger_error('CONFIG_HTTP_TPL file [' . self::$_config[self::CONFIG_HTTP_TPL] . '] is not exist.');
            }
        }
    }

    /**
     * Print only in debug mode
     *
     * @param string $msg
     *
     * @return void
     *
     * @ignore
     */
    private static function _debugPrint($msg)
    {
        if (self::$_config['debug']) {
            echo $msg;
        }
    }

    public static function getErrorStat()
    {
        return self::$_errorStat;
    }

    /**
     * Get Temporary Path
     *
     * @param void
     *
     * @return string
     */
    public static function getTempDir()
    {
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $var = isset($_ENV['TMP']) ? $_ENV['TMP'] : getenv('TMP');
            if ($var) {
                return $var;
            }
            $var = isset($_ENV['TEMP']) ? $_ENV['TEMP'] : getenv('TEMP');
            if ($var) {
                return $var;
            }
            $var = isset($_ENV['USERPROFILE']) ? $_ENV['USERPROFILE'] : getenv('USERPROFILE');
            if ($var) {
                return $var;
            }
            $var = isset($_ENV['windir']) ? $_ENV['windir'] : getenv('windir');
            if ($var) {
                return $var;
            }
            return getenv('SystemRoot') . '\temp';
        }
        // linux / osx
        $var = isset($_ENV['TMPDIR']) ? $_ENV['TMPDIR'] : getenv('TMPDIR');
        if ($var) {
            return $var;
        }
        $tempfile = tempnam(uniqid(rand(), TRUE), '');
        if (file_exists($tempfile)) {
            unlink($tempfile);
            return realpath(dirname($tempfile));
        }
        return realpath('/tmp');
    }

    public static function getOuterPathErrors()
    {
        return self::$_outerPathErrors;
    }

    public static function getAllErrors()
    {
        return self::$_errors;
    }
}
