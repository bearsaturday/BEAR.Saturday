<?php

function smarty_compiler_appinfo($tagArg, &$smarty)
{
    static $app = array();
    
    if (!$app) {
    	$app = BEAR::get('app');
    }
    return 'echo \'' . "{$app['core']['info'][$tagArg]}" . '\';';
}