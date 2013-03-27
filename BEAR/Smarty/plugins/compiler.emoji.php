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
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net/
 */

/**
 * emoji
 *
 * <pre>
 * スタティックな絵文字を表示します。テンプレート生成時のみ動作します。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Smarty
 * @subpackage Plugin
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net/
 *
 * @param string $tagArg
 * @param Smarty &$smarty Smarty object
 *
 * @return string
 */
function smarty_compiler_emoji(
    $tagArg,
    /** @noinspection PhpUnusedParameterInspection */
    &$smarty
) {
    $emoji = BEAR::dependency('BEAR_Emoji')->getAgentEmoji($tagArg);
    // SBの絵文字のエラーを避けるためecho文を使わない
    return '?>' . "{$emoji}" . '<?php ';
    //    return 'echo "' . "{$emoji}" . '";';
}
