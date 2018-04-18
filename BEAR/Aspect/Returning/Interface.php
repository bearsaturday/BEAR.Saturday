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
 * returningアドバイスインターフェイス
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
