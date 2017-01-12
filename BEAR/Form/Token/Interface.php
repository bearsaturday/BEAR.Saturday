<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Form
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */

/**
 * BEAR_Form_Tokenインターフェイス
 *
 * @category  BEAR
 * @package   BEAR_Form
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */
interface BEAR_Form_Token_Interface
{
    /**
     * セッションに新しいトークンを作成
     *
     * @return BEAR_Ro
     */
    public function newSessionToken();

    /**
     * トークンの取得
     *
     */
    public function getToken();

    /**
     * サブミットされたトークンにCSRFの問題はないか
     *
     * @return bool
     */
    public function isTokenCsrfValid();

    /**
     * サブミットされたトークンにPOEの問題はないか
     *
     * @return bool
     */
    public function isTokenPoeValid();
}
