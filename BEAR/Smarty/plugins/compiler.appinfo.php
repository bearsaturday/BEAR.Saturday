<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * appinfo
 *
 * <pre>
 * appinfo(app.ymlの['core']['info']）の値を出力します。テンプレート生成時のみ動作します。
 * </pre>
 *
 *
 *
 *
 *
 * @param string $tagArg
 * @param Smarty &$smarty Smarty object
 *
 * @return string
 */
function smarty_compiler_appinfo(
    $tagArg,
    /* @noinspection PhpUnusedParameterInspection */
    &$smarty
) {
    static $app = array();

    if (! $app) {
        $app = BEAR::get('app');
    }

    return 'echo \'' . "{$app['core']['info'][$tagArg]}" . '\';';
}
