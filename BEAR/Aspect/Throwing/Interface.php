<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Advice
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       https://github.com/bearsaturday
 */

/**
 * throwingアドバイスインターフェイス
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Advice
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
 */
interface BEAR_Aspect_Throwing_Interface
{
    /**
     * throwingアドバイス
     *
     * @param mixed                 $result    エラー結果
     * @param BEAR_Aspect_JoinPoint $joinPoint ジョインポイント
     *
     * @return mixed
     */
    public function throwing($result, BEAR_Aspect_JoinPoint $joinPoint);
}
