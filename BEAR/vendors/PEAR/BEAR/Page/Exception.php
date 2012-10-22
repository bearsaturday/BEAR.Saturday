<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Page
 * @subpackage Exception
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Exception.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Page/BEAR_Page.html
 */
/**
 * ページ例外クラス
 *
 * @category  BEAR
 * @package   BEAR_Page
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Exception.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Page/BEAR_Page.html
 */
class BEAR_Page_Exception extends BEAR_Exception
{

    /**
     * コンストラクタ
     *
     * @param int    $httpStatus HTTPレスポンスコード
     * @param string $message    メッセージ
     * @param int    $severity   深刻度
     */
    function __construct($message, $httpStatus, $severity = E_ERROR)
    {
        parent::__construct($message, $httpStatus, $severity);
    }
}