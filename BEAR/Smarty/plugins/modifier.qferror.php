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
 * @version    SVN: Release: @package_version@ $Id: modifier.qferror.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
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
 * @category   BEAR
 * @package    BEAR_Smarty
 * @subpackage Plugin
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: modifier.qferror.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/

 * @param array $errors QuickFormエラー
 *
 * @return string
 */
function smarty_modifier_qferror($errors)
{
    $result = '';
    foreach ($errors as $error) {
        $result .= '<div style="color:red" class="qferror">' . $error . '</div>';
    }
    return $result . '<br />';
}