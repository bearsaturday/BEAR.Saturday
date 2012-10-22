<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Aspect
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Aspect/BEAR_Aspect.html
 */
/**
 * aroundアドバイスインターフェイス
 *
 * <pre>
 * aroundアドバイスを実装するメソッドが使用するインターフェイスです。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Aspect
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Aspect.html
 */
/**
 * aroundインターフェイス
 *
 * @param array                 $values    バリュー
 * @param BEAR_Aspect_JoinPoint $joinPoint ジョインポイント
 *
 * @return void
 */
interface BEAR_Aspect_Around_Interface
{

    /**
     * aroundアドバイス
     *
     * @param array                 $values    バリュー
     * @param BEAR_Aspect_JoinPoint $joinPoint ジョインポイント
     *
     * @return void
     */
    public function around(array $values, BEAR_Aspect_JoinPoint $joinPoint);
}