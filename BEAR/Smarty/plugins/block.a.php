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
 * @version    SVN: Release: @package_version@ $Id: block.a.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */

/**
 * 拡張aタグ
 *
 * <pre>
 * 拡張した属性のaタグを提供します。
 *
 * click string onClickイベント
 * value mixed スカラー値、配列、オブジェクト（プロパティのみ）
 *
 * Example.1 配列やオブジェクトを渡す
 *
 * {a href="/" val=$values}
 *
 * Example.2 ページのonClickハンドラをコール。自分に向けるときはhrefを省略できます
 *
 * {a click=print}
 *
 * Example.3 標準aタグと共存できます
 *
 * {a click="print" val=$values class="class_name" href="/home/"}ホームへ{/a}
 *
 * $params
 *  'val'   mixed  変数（スカラー値、配列、オブジェクト）
 *  'click' string クリックイベント
 *
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Smarty
 * @subpackage Plugin
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: block.a.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 *
 * @param string $params  パラメーター
 * @param string $content HTML
 * @param Smarty &$smarty &Smarty object
 * @param bool   &$repeat &$repeat 呼び出し回数
 *
 * @return string
 */
function smarty_block_a($params, $content,
    /** @noinspection PhpUnusedParameterInspection */
    &$smarty, &$repeat)
{
    if ($repeat || !$content) {
        return '';
    }
    // hrefの省略を有効なHTMLにする
    $params['href'] = (isset($params['href'])) ? $params['href'] : $_SERVER['REQUEST_URI'];
    //active link
    if (isset($params['click'])) {
        $values[BEAR_Page::KEY_CLICK_NAME] = $params['click'];
        unset($params['click']);
    }
    // value
    $values[BEAR_Page::KEY_CLICK_VALUE] = isset($params['val']) ? $params['val'] : '';
    $values += $_GET;
    $phref = parse_url($params['href']);
    $href = $phref['path'];
    $params['href'] = $href . '?' . http_build_query($values);
    unset($params['val']);
    $result = '';
    /** @noinspection PhpWrongForeachArgumentTypeInspection */
    foreach ($params as $key => $value) {
        $result .= ' ' . $key . '="' . $value . '"';
    }
    $result = "<a{$result}>{$content}</a>";
    return $result;
}
