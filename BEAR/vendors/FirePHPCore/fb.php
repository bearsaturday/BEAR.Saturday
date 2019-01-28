<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

if (! class_exists('FirePHP')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'FirePHP.class.php';
}

/**
 * Sends the given data to the FirePHP Firefox Extension.
 * The data can be displayed in the Firebug Console or in the
 * "Server" request tab.
 *
 * @see http://www.firephp.org/Wiki/Reference/Fb
 *
 * @throws Exception
 *
 * @return true
 */
function fb()
{
    $instance = FirePHP::getInstance(true);

    $args = func_get_args();

    return call_user_func_array([$instance, 'fb'], $args);
}

class fb
{
    /**
     * Enable and disable logging to Firebug
     *
     * @see FirePHP->setEnabled()
     *
     * @param bool $Enabled TRUE to enable, FALSE to disable
     */
    public static function setEnabled($Enabled)
    {
        $instance = FirePHP::getInstance(true);
        $instance->setEnabled($Enabled);
    }

    /**
     * Check if logging is enabled
     *
     * @see FirePHP->getEnabled()
     *
     * @return bool TRUE if enabled
     */
    public static function getEnabled()
    {
        $instance = FirePHP::getInstance(true);

        return $instance->getEnabled();
    }

    /**
     * Specify a filter to be used when encoding an object
     *
     * Filters are used to exclude object members.
     *
     * @see FirePHP->setObjectFilter()
     *
     * @param string $Class  The class name of the object
     * @param array  $Filter An array or members to exclude
     */
    public static function setObjectFilter($Class, $Filter)
    {
        $instance = FirePHP::getInstance(true);
        $instance->setObjectFilter($Class, $Filter);
    }

    /**
     * Set some options for the library
     *
     * @see FirePHP->setOptions()
     *
     * @param array $Options The options to be set
     */
    public static function setOptions($Options)
    {
        $instance = FirePHP::getInstance(true);
        $instance->setOptions($Options);
    }

    /**
     * Get options for the library
     *
     * @see FirePHP->getOptions()
     *
     * @return array The options
     */
    public static function getOptions()
    {
        $instance = FirePHP::getInstance(true);

        return $instance->getOptions();
    }

    /**
     * Log object to firebug
     *
     * @see http://www.firephp.org/Wiki/Reference/Fb
     *
     * @throws Exception
     *
     * @return true
     */
    public static function send()
    {
        $instance = FirePHP::getInstance(true);
        $args = func_get_args();

        return call_user_func_array([$instance, 'fb'], $args);
    }

    /**
     * Start a group for following messages
     *
     * Options:
     *   Collapsed: [true|false]
     *   Color:     [#RRGGBB|ColorName]
     *
     * @param string $Name
     * @param array  $Options OPTIONAL Instructions on how to log the group
     *
     * @return true
     */
    public static function group($Name, $Options = null)
    {
        $instance = FirePHP::getInstance(true);

        return $instance->group($Name, $Options);
    }

    /**
     * Ends a group you have started before
     *
     * @throws Exception
     *
     * @return true
     */
    public static function groupEnd()
    {
        return self::send(null, null, FirePHP::GROUP_END);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::LOG
     *
     * @param mixes  $Object
     * @param string $Label
     *
     * @throws Exception
     *
     * @return true
     */
    public static function log($Object, $Label = null)
    {
        return self::send($Object, $Label, FirePHP::LOG);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::INFO
     *
     * @param mixes  $Object
     * @param string $Label
     *
     * @throws Exception
     *
     * @return true
     */
    public static function info($Object, $Label = null)
    {
        return self::send($Object, $Label, FirePHP::INFO);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::WARN
     *
     * @param mixes  $Object
     * @param string $Label
     *
     * @throws Exception
     *
     * @return true
     */
    public static function warn($Object, $Label = null)
    {
        return self::send($Object, $Label, FirePHP::WARN);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::ERROR
     *
     * @param mixes  $Object
     * @param string $Label
     *
     * @throws Exception
     *
     * @return true
     */
    public static function error($Object, $Label = null)
    {
        return self::send($Object, $Label, FirePHP::ERROR);
    }

    /**
     * Dumps key and variable to firebug server panel
     *
     * @see FirePHP::DUMP
     *
     * @param string $Key
     *
     * @throws Exception
     *
     * @return true
     */
    public static function dump($Key, $Variable)
    {
        return self::send($Variable, $Key, FirePHP::DUMP);
    }

    /**
     * Log a trace in the firebug console
     *
     * @see FirePHP::TRACE
     *
     * @param string $Label
     *
     * @throws Exception
     *
     * @return true
     */
    public static function trace($Label)
    {
        return self::send($Label, FirePHP::TRACE);
    }

    /**
     * Log a table in the firebug console
     *
     * @see FirePHP::TABLE
     *
     * @param string $Label
     * @param string $Table
     *
     * @throws Exception
     *
     * @return true
     */
    public static function table($Label, $Table)
    {
        return self::send($Table, $Label, FirePHP::TABLE);
    }
}
