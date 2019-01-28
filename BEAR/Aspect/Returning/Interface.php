<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * returningアドバイスインターフェイス
 */
interface BEAR_Aspect_Returning_Interface
{
    /**
     * returningアドバイス
     *
     * @param $result
     */
    public function returning($result, BEAR_Aspect_JoinPoint $joinPoint);
}
