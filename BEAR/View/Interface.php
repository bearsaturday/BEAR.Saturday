<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */
/**
 * BEAR_Viewインターフェイス
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */
interface BEAR_View_Interface
{
    /**
     * ビューに値をセット
     *
     * @param array $values バリュー
     *
     * @return void
     */
    public function set(array $values);

    /**
     * 表示
     *
     * ビューにセットされたバリューをテンプレートに適用して画面表示します。
     *
     * @param string $tplName テンプレート名
     * @param array  $options オプション
     *
     * @return void
     */
    public function display($tplName = null, array $options = array());
}
