<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * emoji
 *
 * <pre>
 * スタティックな絵文字を表示します。テンプレート生成時のみ動作します。
 * </pre>
 *
 * @param string $tagArg
 * @param Smarty &$smarty Smarty object
 *
 * @return string
 */
function smarty_compiler_emoji(
    $tagArg,
    /* @noinspection PhpUnusedParameterInspection */
    &$smarty
) {
    $emoji = BEAR::dependency('BEAR_Emoji')->getAgentEmoji($tagArg);
    // SBの絵文字のエラーを避けるためecho文を使わない
    return '?>' . "{$emoji}" . '<?php ';
    //    return 'echo "' . "{$emoji}" . '";';
}
