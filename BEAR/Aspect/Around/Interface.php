<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * aroundアドバイスインターフェイス
 */
interface BEAR_Aspect_Around_Interface
{
    /**
     * aroundアドバイス
     *
     * @param array                 $values    バリュー
     * @param BEAR_Aspect_JoinPoint $joinPoint ジョインポイント
     *
     * @return mixed
     */
    public function around(array $values, BEAR_Aspect_JoinPoint $joinPoint);
}
