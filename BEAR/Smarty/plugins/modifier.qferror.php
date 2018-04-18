<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
 *
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
