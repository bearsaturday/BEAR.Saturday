<?php

/**
 * BEAR_Smarty
 * 
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Smarty
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: modifier.printa.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Smarty/BEAR_Smarty.html
 */
/**
 * print_a表示
 *
 * <pre>
 * デバック用表示（print_a）します
 *
 * Example
 * </pre>
 * <code>
 * {$body|printa}
 * </code>
 *
 * @param string $string 文字列
 * 
 * @return $string
 */
function smarty_modifier_printa($string)
{
    if (!function_exists('print_a')) {
        include 'BEAR/inc/debuglib.php';
    }
    $string = print_a($string, "return:true");
    return $string;
}