<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * returningアドバイスインターフェイス
 */
interface BEAR_Aspect_Returning_Interface
{
    /**
     * returningアドバイス
     *
     * @param                       $result
     * @param BEAR_Aspect_JoinPoint $joinPoint
     *
     * @return mixed
     */
    public function returning($result, BEAR_Aspect_JoinPoint $joinPoint);
}
