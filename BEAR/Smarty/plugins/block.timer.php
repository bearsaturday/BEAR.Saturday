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
 * @version    SVN: Release: @package_version@ $Id: block.timer.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */

/**
 * timer
 *
 * <pre>
 * 指定時間によってコンテンツを制限します
 *
 * Example
 * </pre>
 * <code>
 * // 17:00から明くる日の7:30までコンテンツを表示します
 * {timer from="17:00" to="+1 day 07:30"}CME 17:00-7:30<br />
 * </code>
 * <pre>
 * 'from' string 開始時間 パースする文字列で、GNU » Date Input Formats 形式に準拠したもの
 * 'to'   string 終了時間
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Smarty
 * @subpackage Plugin
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: block.timer.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 *
 * @param string $params  パラメーター
 * @param string $content HTML
 * @param Smarty &$smarty &Smarty object
 * @param bool   &$repeat &$repeat 呼び出し回数
 *
 * @return string
 * @see http://jp2.php.net/manual/ja/function.strtotime.php strtotime
 * @see http://www.gnu.org/software/tar/manual/html_node/Date-input-formats.html Date-input-formats
 */
function smarty_block_timer($params, $content, &$smarty, &$repeat)
{
    $from = (strtotime($params['from']));
    $to = (strtotime($params['to']));
    $now = time();
    if ($now >= $from && $now <= $to) {
        $result = $content;
    } else {
        $result = '';
    }
    return $result;
}
