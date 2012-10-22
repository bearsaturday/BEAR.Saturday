<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR
 * @subpackage Dependency
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 */
/**
 * BEAR Baseインターフェイス
 *
 * <pre>
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR
 * @subpackage Dependency
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 */
interface BEAR_Injector_Interface
{
    /**
     * インジェクト
     *
     * <pre>
     * 指定オブジェクトに必要なサービスを注入します。
     * </pre>
     *
     * @param object $object インジェクトされるオブジェクト
     * @param array  $config 設定
     *
     * @return string
     */
     public static function inject(&$object, $config);
}