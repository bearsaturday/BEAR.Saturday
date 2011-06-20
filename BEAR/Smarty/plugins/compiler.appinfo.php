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
 * appinfo
 *
 * <pre>
 * appinfo(app.ymlの['core']['info']）の値を出力します。テンプレート生成時のみ動作します。
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
function smarty_compiler_appinfo($tagArg, &$smarty)
{
    static $app = array();

    if (!$app) {
        $app = BEAR::get('app');
    }
    return 'echo \'' . "{$app['core']['info'][$tagArg]}" . '\';';
}