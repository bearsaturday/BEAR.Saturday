<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Advice
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Interface.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link       https://github.com/bearsaturday
 */

/**
 * beforeアドバイスインターフェイス
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Advice
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Interface.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net
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
