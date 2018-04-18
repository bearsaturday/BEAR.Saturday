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
 * beforeアドバイスインターフェイス
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
