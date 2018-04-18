<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
 */

/**
 * throwingアドバイスインターフェイス
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
