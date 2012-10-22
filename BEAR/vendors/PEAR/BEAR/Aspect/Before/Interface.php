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
 * beforeアドバイスインターフェイス
 *
 * <pre>
 * beforeアドバイスを実装するメソッドが使用するインターフェイスです。
 * beforeアドバイスはジョインポイントのメソッドが実行される前に織り込まれるアドバイスです。
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
interface BEAR_Aspect_Before_Interface
{

    /**
     * beforeアドバイス
     *
     * @param array                 $values    バリュー
     * @param BEAR_Aspect_JoinPoint $joinPoint ジョインポイント
     * 
     * @return void
     */
    public function before(array $values, BEAR_Aspect_JoinPoint $joinPoint);
}