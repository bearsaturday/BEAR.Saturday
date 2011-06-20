<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Adaptor
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * BEAR_Agent_Adaptor_Interface
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Adaptor
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
 */
interface BEAR_Agent_Adaptor_Interface
{
    /**
     * サブミット処理
     *
     * 各UAに応じたサブミット処理（絵文字エンティティや文字コード変換）をします。
     *
     * @return string
     */
    public function submit();
}