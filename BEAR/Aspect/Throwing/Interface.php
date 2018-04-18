<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * throwingアドバイスインターフェイス
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
