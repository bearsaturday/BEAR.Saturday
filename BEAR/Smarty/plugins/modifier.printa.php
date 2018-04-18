<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
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
 *
 *
 *
 *
 * @param string $string 文字列
 *
 * @return string
 */
function smarty_modifier_printa($string)
{
    if (! function_exists('print_a')) {
        /** @noinspection PhpIncludeInspection */
        include 'BEAR/vendors/debuglib.php';
    }
    $string = print_a($string, 'return:true');

    return $string;
}
