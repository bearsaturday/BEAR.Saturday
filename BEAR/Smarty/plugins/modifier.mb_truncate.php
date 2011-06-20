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
 * @version    SVN: Release: @package_version@ $Id: modifier.mb_truncate.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */

/**
 * Smarty mb_truncate modifier
 *
 * <pre>
 * マルチバイト文字をtruncateします。
 * カットする文字列の幅（バイト数）、追加する末尾文字、文字コードを指定します。
 *
 * Example
 *
 * </pre>
 * <code>
 * {$body|mb_truncate:"30":'more..':'utf-8'}
 * </code>
 * <pre>
 *
 * $params
 *
 *  'in'   mixed  カンマ区切りで含まれていたら表示。
 *  'out'  string カンマ区切りで含まれていなかったら表示
 *  'func' string ユーザー関数
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Smarty
 * @subpackage Plugin
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: modifier.mb_truncate.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/

 * @param string $string 文字列
 * @param int    $length 長さ
 * @param string $etc    'more'文字
 * @param string $encode 文字コード
 *
 * @return string
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 */
function smarty_modifier_mb_truncate($string, $length = 80, $etc = '…', $encode = "UTF-8")
{
    $result = '';
    if (mb_strwidth($string) > $length) {
        $length -= mb_strwidth($etc, $encode);
        $result = mb_strcut($string, 0, $length, $encode) . $etc;
    } else {
        $result = $string;
    }
    return $result;
}