<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Form
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * BEAR_Form_Token
 *
 * @category  BEAR
 * @package   BEAR_Form
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 *
 */
class BEAR_Form_Token extends BEAR_Base implements BEAR_Form_Token_Interface
{
    /**
     * セッショントークンキー名
     *
     * @var string
     */
    const SESSION_TOKEN = 'stoken';

    /**
     * POE(Post Once Exactly)用トークンキー配列セッションキー名
     *
     * @var string
     */
    const SESSION_POE = 'poe_key';

    /**
     * CSRFキーの長さ
     *
     * @var int
     */
    const SESSION_CSRF_LEN = 20;

    /**
     * POEキーの長さ
     *
     * @var int
     */
    const SESSION_POE_LEN = 20;

    /**
     * @var BEAR_Session
     */
    protected $_tokenStrage;

    /**
     * Submit token
     *
     * @var string
     */
    protected $_submitToken;

    /**
     * @see BEAR_Base::onInject()
     */
    public function onInject()
    {
        $this->_tokenStrage = BEAR::dependency('BEAR_Session');
        if (isset($_POST['_token'])) {
            $token = $_POST['_token'];
        } elseif (isset($_GET['_token'])) {
            $token = $_GET['_token'];
        } else {
            $token = '';
        }
        $this->_submitToken = $token;
    }

    /**
     * トークン作成
     *
     * @return BEAR_Ro
     */
    public function newSessionToken()
    {
        $csrfToken = $this->_getRndToken(session_id(), self::SESSION_CSRF_LEN);
        $poeToken = $this->_getRndToken(uniqid(mt_rand(), true), self::SESSION_POE_LEN);
        $token = $csrfToken . $poeToken;
        $this->_tokenStrage->set(self::SESSION_TOKEN, $token);
    }

    /**
     * 任意の長さの乱数文字列の取得
     *
     * @param string $salt
     * @param int    $length
     *
     * @return string
     */
    protected function _getRndToken($salt, $length = null)
    {
        $sha = sha1($salt);
        if ($length === null) {
            return $sha;
        }
        $token = substr($sha, 0, $length);
        return $token;
    }

    /**
     * @see BEAR_Form_Token_Interface::getToken()
     */
    public function getToken()
    {
        $token = $this->_tokenStrage->get(self::SESSION_TOKEN);
        if ($token) {
            return $token;
        }
        // セッション不使用
        return sha1(session_id());
    }

    /**
     * @see BEAR_Form_Token_Interface::isTokenCsrfValid()
     */
    public function isTokenCsrfValid()
    {
        $sessToken = $this->_tokenStrage->get(self::SESSION_TOKEN);
        $sessToken = substr($sessToken, 0, self::SESSION_POE_LEN);
        $submitToken = substr($this->_submitToken, 0, self::SESSION_CSRF_LEN);
        $isValid = is_string($sessToken) && ($sessToken === $submitToken);
        return $isValid;
    }

    /**
     * @see BEAR_Form_Token_Interface::isTokenPoeValid()
     */
    public function isTokenPoeValid()
    {
        $poes = $this->_tokenStrage->get(self::SESSION_POE);
        $poes = is_array($poes) ? $poes : array();
        $isDoubleSubmit = in_array($this->_submitToken, $poes);
        if ($isDoubleSubmit) {
            return false;
        }
        // insert key
        $poes[] = $this->_submitToken;
        if (count($poes) > 3) {
            $poes = array_slice($poes, 1, 3);
        }
        $this->_tokenStrage->set(self::SESSION_POE, $poes);
        return true;
    }
}
