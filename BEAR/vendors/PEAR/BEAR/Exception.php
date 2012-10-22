<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Exception
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Exception.php 821 2009-08-04 09:45:12Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR/BEAR.html
 */
/**
 *
 *
 * @category  BEAR
 * @package   BEAR_Exception
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Exception.php 821 2009-08-04 09:45:12Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR/BEAR.html
 */
class BEAR_Exception extends Exception
{

    /**
     * 設定　クラス名
     */
    const CONFIG_CLASS = 'class';

    /**
     * 設定　コード
     */
    const CONFIG_CODE = 'code';

    /**
     * 設定　インフォ
     */
    const CONFIG_INFO = 'info';

    /**
     * 設定　リダイレクトURL
     */
    const CONFIG_REDIRECT = 'redirect';

    /**
     * 例外情報
     *
     * @var array
     *
     */
    protected $_info = array();

    /**
     * クラス
     *
     * @var string
     *
     */
    protected $_class;

    /**
     * リダイレクト
     *
     * @var string
     *
     */
    protected $_redirect;

    /**
     * コンフィグデフォルト
     *
     * @var array
     */
    protected $_default = array('class' => null,
        'code' => BEAR::CODE_ERROR,
        'info' => array(),
        'redirect' => null);

    /**
     * コンストラクタ
     *
     * @param string $message メッセージ
     * @param array  $config  コンフィグ
     */
    public function __construct($msg, array $config = array())
    {
        $config = array_merge($this->_default, (array)$config);
        parent::__construct($msg);
        $this->code = $config['code']; //native
        $this->_class = get_class($this);
        $this->_info = (array)$config['info'];
        $this->_redirect = $config['redirect'];
    }

    /**
     * 情報取得
     */
    public function getInfo()
    {
        return $this->_info;
    }

    /**
     * リダイレクト先取得
     */
    public function getRedirect()
    {
        return $this->_redirect;
    }

    /**
     * 文字列として返す
     *
     * @return void
     *
     */
    public function __toString()
    {
        $str = "exception '" . get_class($this) . "'\n" . "class::code '" . $this->_class . "::" . $this->code . "' \n";
        $str .= "with message '" . $this->message . "' \n" . "information " . var_export($this->_info, true) . " \n";
        $str .= "redirect to '" . $this->_redirect . "' \n";
        $str .= "Stack trace:\n" . "  " . str_replace("\n", "\n  ", $this->getTraceAsString());
        return $str;
    }
}
