<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
 */
/**
 * BEAR Baseインターフェイス
 *
 * <pre>
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Aspect
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Agent.html
 */
interface BEAR_Agent_Adaptor_Interface
{
    /**
     * サブミット処理
     *
     * <pre>
     * エージェントの設定に応じて絵文字エンティティや文字コード変換をします。
     * </pre>
     *
     * @return string
     */
     public function submit();
}