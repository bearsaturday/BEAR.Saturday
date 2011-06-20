<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Advice
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Interface.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * afterアドバイスインターフェイス
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Advice
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Interface.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
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