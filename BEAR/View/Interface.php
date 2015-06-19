<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Interface.php 889 2009-09-16 00:22:54Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */
/**
 * BEAR_Viewインターフェイス
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Interface.php 889 2009-09-16 00:22:54Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
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
