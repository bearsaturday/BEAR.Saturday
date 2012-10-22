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
 * @version   SVN: Release: $Id: modifier.mb_truncate.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Smarty/BEAR_Smarty.html
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