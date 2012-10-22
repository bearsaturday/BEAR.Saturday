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
 * @version   SVN: Release: $Id: modifier.qferror.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Smarty/BEAR_Smarty.html
 */
/**
 * QuickFormエラー
 *
 * <pre>
 * QuickFormのエラーを表示します。cssクラスは"qferror"です。
 * 
 * Example
 * </pre>
 * <code>
 * {$form.errors|qferror}
 * </code>
 * 
 * @param array $errors QuickFormエラー
 * 
 * @return string
 * 
 */
function smarty_modifier_qferror($errors)
{
    $result = '';
    foreach ($errors as $error) {
        $result .= '<div style="color:red" class="qferror">' . $error . '</div>';
    }
    return $result . '<br />';
}