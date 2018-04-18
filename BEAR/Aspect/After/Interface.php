<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * afterアドバイスインターフェイス
 */
interface BEAR_Aspect_After_Interface
{
    /**
     * afterアドバイス
     *
     * @param array                 $result    結果
     * @param BEAR_Aspect_JoinPoint $joinPoint ジョインポイント
     *
     * @return mixed
     */
    public function after($result, BEAR_Aspect_JoinPoint $joinPoint);
}
