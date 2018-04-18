<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * BEAR_Form_Tokenインターフェイス
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
