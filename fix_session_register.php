<?php
// Fix for removed Session functions
if (! function_exists('session_register')) {
    function session_register()
    {
        $args = func_get_args();
        foreach ($args as $key){
            $_SESSION[$key] = $GLOBALS[$key];
        }
    }

    function session_is_registered($key)
    {
        return isset($_SESSION[$key]);
    }

    function session_unregister($key)
    {
        unset($_SESSION[$key]);
    }
}
