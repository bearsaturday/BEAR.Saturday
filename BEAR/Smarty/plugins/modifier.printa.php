<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Smarty
 * @subpackage Plugin
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: modifier.printa.php 2538 2011-06-12 17:37:53Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
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
 * @category   BEAR
 * @package    BEAR_Smarty
 * @subpackage Plugin
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: modifier.printa.php 2538 2011-06-12 17:37:53Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 *
 * @param string $string 文字列
 *
 * @return string
 */
function smarty_modifier_printa($string)
{
    if (!function_exists('print_a')) {
        /** @noinspection PhpIncludeInspection */
        include 'BEAR/vendors/debuglib.php';
    }
    $string = print_a($string, "return:true");
    return $string;
}