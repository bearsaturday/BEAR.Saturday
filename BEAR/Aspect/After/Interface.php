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
 * afterアドバイスインターフェイス
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
