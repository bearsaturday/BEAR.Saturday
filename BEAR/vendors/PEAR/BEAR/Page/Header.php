<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Page
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Header.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Page/BEAR_Page.html
 */
/**
 * BEAR_Page_Headerクラス
 *
 * @category  BEAR
 * @package   BEAR_Page
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Header.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Page/BEAR_Page.html
 * @abstract
 */
class BEAR_Page_Header extends BEAR_Base implements BEAR_Page_Header_Interface
{
    /**
     * HTTP出力ヘッダー
     *
     * @var array
     */
    private $_headers = array();

    /**
     * モバイル出力ヘッダー
     *
     * @var string
     */
    private $_mobileHeader = 'application/xhtml+xml';

    /**
     * コンストラクタ
     *
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * ヘッダー出力
     *
     * <pre>
     * ヘッダーを出力用にバッファリングします。
     * 引数は配列または文字列で指定できます。
     * スタティック変数として保存されBEAR_Mainで出力バッファを
     * フラッシュする時に送出されます。
     * 同じ<
     * /pre>
     *
     * @param mixed $header HTTPヘッダー
     *
     * @return void
     * @static
     */
    public function setHeader($header)
    {
        $this->_headers[] = $header;
    }

    protected function setMobileHeader($header = 'text/html')
    {
        $this->_mobileHeader = $header;
    }

    /**
     * ヘッダーの取得
     *
     * @return array ヘッダー
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * ヘッダーのフラッシュ
     *
     * <pre>
     * ページにヘッダーを取得します。
     * 通常はページ出力時に自動で出力されます。
     * </pre>
     *
     * @return void
     * @static
     */
    public function flushHeader()
    {
        $this->_log->log('Headers', $this->_headers);
        foreach ($this->_headers as $header) {
            if (is_string($header)) {
                header($header);
            }
        }
    }

    /**
     * リダイレクト
     *
     * <pre>Locationヘッダーを用いてページの移動を行います。
     * クッキーが対応してないエージェントの場合はクエリーに
     * セッションIDを付加します。
     *
     * .(dot)を指定すると同一ページのリフレッシュになります。
     * ページが完全に移動した場合は$config['permanent']をtrueにすると
     * 301ヘッダーを付加してリダイレクトしボットなどに移転を知らせます。
     *
     * -----------------------------------------
     *
     * Example 1. リダイレクト
     * </pre>
     * <code>
     *  $header->redirect('http://www.example.co.jp/');
     * </code>
     * <pre>
     * Example 2. リダイレクト（301 パーマネント)
     * </pre<
     * <code>
     * $header->redirect('/', array('permanent'=>true));
     * </code>
     * <pre>
     * Example 3. 値を渡してリロード
     * </pre
     * <code>
     * // onInit($args)の$argsに渡されます
     * $header->redirect('.', array('args'=$values);
     * </code>
     *
     * <b>$options</b>
     *
     * 'val'       string セッション利用して値を次ページのonInit($args)に変数を渡す値
     * 'click'     string コールするonClickハンドラ
     * 'permanent' bool   301ヘッダー(パーマネントムーブ)を出力するか
     *
     * @param string $url     URL
     * @param array  $options オプション
     *
     * @return      void
     */
    public function redirect($url, array $options = array('val'=>null, 'click'=>null, 'permanent'=>false))
    {
        // .なら現在のファイルでページキャッシュもクリアする
        if ($url == '.' || $url == './') {
            $url = $_SERVER['PHP_SELF'];
            $page = BEAR::get('page');
            $page->clearPageCache();
        }
        // 相対パスならフルパスに変換
        $remoteAddr = $_SERVER["HTTP_HOST"];
        if (strpos($url, "http") === false) {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $url = "https://{$remoteAddr}$url";
            } else {
                $url = "http://{$remoteAddr}$url";
            }
        }
        // 携帯の場合などクッキーが使用できない環境ではセッションクエリーをURLに付加
        $sessionName = session_name();
        $sessionId = session_id();
        if (!isset($_COOKIE[$sessionName]) && $sessionId && isset($options['session']) && $options['session']) {
            // セッションクエリーが付いてれば消去
            //        $url = preg_replace("/&*{$sessionName}=[^&]+/is", '', $url);
            $url = preg_replace("/([&\\?]){$sessionName}=[^&]?/is", '$1', $url);
            $con = (strpos($url, "?")) ? '&' : '?';
            $url .= "{$con}{$sessionName}={$sessionId}";
            if (strlen($sessionId) != 32) {
                trigger_error('session key error' . $url, E_USER_WARNING);
            }
        }
        //argsオプション
        if(isset($options['val'])) {
          $query = array('_cv' => $options['val']);
        } else {
          $query = '';
        }
        if (isset($options['click'])) {
            $click = array(
                BEAR_Page::KEY_CLICK_NAME => $options['click']);
            if (is_array($query)) {
                $query = array_merge($query, $click);
            } else {
                $query = array_merge(array(
                    '_sc' => $query), $click);
            }
        }
        if ($query) {
            $url = $url . '?' . http_build_query($query);
        }
        if (isset($options['sval'])) {
            $session = BEAR::dependency('BEAR_Session', 'session');
            /* @var $session BEAR_Session */
            $session->set('val', $options['sval']);
        }
        if (isset($options['permanent']) && $options['permanent']) {
            $this->setHeader("HTTP/1.1 301 Moved Permanently");
        }
        //　ロケーションヘッダー出力
        $this->setHeader("Location: {$url}");
        $this->_log->log('redirect', $url);
        $this->flushHeader();
        exit();
    }

    /**
     * リクエストヘッダーの取得
     *
     * @param string $header HTTPヘッダー名
     *
     * @return string|false HTTPヘッダー値、みつからなければfalse
     */
    public function getRequestHeader($header)
    {
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (isset($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (!empty($headers[$header])) {
                return $headers[$header];
            }
        }
        return false;
    }
}
