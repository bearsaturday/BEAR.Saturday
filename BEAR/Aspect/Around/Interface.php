<?php
/**
 * BEAR
 *
 * PHP versions 5
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
