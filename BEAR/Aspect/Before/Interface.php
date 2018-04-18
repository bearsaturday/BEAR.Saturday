<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * beforeアドバイスインターフェイス
 */
interface BEAR_Aspect_Before_Interface
{
    /**
     * beforeアドバイス
     *
     * @param array                 $values    バリュー
     * @param BEAR_Aspect_JoinPoint $joinPoint ジョインポイント
     */
    public function before(array $values, BEAR_Aspect_JoinPoint $joinPoint);
}
