<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * BEAR_Viewインターフェイス
 */
interface BEAR_View_Interface
{
    /**
     * ビューに値をセット
     *
     * @param array $values バリュー
     */
    public function set(array $values);

    /**
     * 表示
     *
     * ビューにセットされたバリューをテンプレートに適用して画面表示します。
     *
     * @param string $tplName テンプレート名
     * @param array  $options オプション
     */
    public function display($tplName = null, array $options = array());
}
