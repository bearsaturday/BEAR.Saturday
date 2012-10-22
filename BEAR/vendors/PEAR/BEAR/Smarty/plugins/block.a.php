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
 * @version   SVN: Release: $Id: block.a.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Smarty/BEAR_Smarty.html
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
 * @param string $params  パラメーター
 * @param string $content HTML
 * @param Smarty &$smarty &Smarty object
 * @param bool   &$repeat &$repeat 呼び出し回数
 *
 * @return string
 */
function smarty_block_a($params, $content, &$smarty, &$repeat)
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
    foreach ($params as $key => $value) {
        $result .= ' ' . $key . '="' . $value . '"';
    }
    $result = "<a{$result}>{$content}</a>";
    return $result;
}
