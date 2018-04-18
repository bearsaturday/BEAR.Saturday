<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * エージェントブロック関数
 *
 * <pre>
 * エージェントによって表示/非表示を制御します。
 * エージェントの指定は大文字でも小文字問いません。
 *
 * Example
 * </pre>
 *
 * <code>
 * {agent in='docomo,au'}ドコモとＡＵだけ表示{/agent}
 * {agent out='softbank'}SBのみ非表示{/agent}
 * {agent in='iphone' func='upper_case'}iPhoneのみ大文字で{/agent}
 * </code>
 * <pre>
 *
 * $params
 *
 *  'in'   mixed  カンマ区切りで含まれていたら表示。
 *  'out'  string カンマ区切りで含まれていなかったら表示
 *  'func' string ユーザー関数
 * </pre>
 *
 *
 *
 *
 *
 * @param string $params  パラメーター
 * @param string $content HTML
 * @param Smarty &$smarty &Smarty object
 * @param bool   &$repeat &$repeat 呼び出し回数
 *
 * @return string
 */
function smarty_block_agent(
    $params,
    $content,
    /* @noinspection PhpUnusedParameterInspection */
    &$smarty,
    &$repeat
) {
    $ua = strtolower(BEAR::dependency('BEAR_Agent')->getUa());
    //開始タグ
    if (is_null($content)) {
        $valid = false;
        if (array_key_exists('in', $params)) {
            $in = explode(',', $params['in']);
            if (in_array($ua, $in, true)) {
                $valid = true;
            }
        }
        if (! $valid) {
            if (array_key_exists('out', $params)) {
                $out = explode(',', $params['out']);
                if (! in_array($ua, $out, true)) {
                    $valid = true;
                }
            }
        }
        if (! $valid) {
            $repeat = false;
        }
    } else {
        if (array_key_exists('func', $params)) {
            assert(function_exists($params['func']));

            return call_user_func($params['func'], $content);
        }

        return $content;
    }

    return '';
}
