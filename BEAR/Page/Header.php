<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * ヘッダー
 *
 *
 *
 *
 *
 * @Singleton
 */
class BEAR_Page_Header extends BEAR_Base implements BEAR_Page_Header_Interface
{
    /**
     * @var BEAR_Log
     */
    protected $_log;

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
     * Inject
     */
    public function onInject()
    {
        $this->_log = BEAR::dependency('BEAR_Log');
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
     * @static
     */
    public function setHeader($header)
    {
        $this->_headers[] = $header;
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
     * <pre>
     * Locationヘッダーを用いてページの移動を行います。
     * クッキーが対応してないエージェントの場合はクエリーに
     * セッションIDを付加します。
     *
     * $uriは絶対URIを指定しますが、ホスト名を付加しないで指定した場合内部で付加します。
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
     * Example 2. リダイレクト（301 パーマネント
     * </pre<
     * <code>
     * $header->redirect('/', array('permanent' => true));
     * </code>
     * <pre>
     * Example 3. 値を渡してリロード
     * </pre>
     * <code>
     * // onInit($args)の$argsに渡されます
     * $header->redirect('.', array('click' => 'delete', 'val' => $values);
     * </code>
     *
     * <b>$options</b>
     *
     * 'val'       string セッション利用して値を次ページのonInit($args)に変数を渡す値
     * 'click'     string コールするonClickハンドラ
     * 'permanent' bool   301ヘッダー(パーマネントムーブ)を出力するか
     *
     * @param string $uri     URL
     * @param array  $options オプション
     */
    public function redirect($uri, array $options = array('val' => null, 'click' => null, 'permanent' => false))
    {
        // .なら現在のファイルでページキャッシュもクリアする
        if ($uri == '.' || $uri == './') {
            $uri = $_SERVER['PHP_SELF'];
            $page = BEAR::get('page');
            $page->clearPageCache();
        }
        // ホストがないならホストを付加
        $remoteAddr = $_SERVER['HTTP_HOST'];
        if (strpos($uri, 'http') === false) {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $uri = "https://{$remoteAddr}$uri";
            } else {
                $uri = "http://{$remoteAddr}$uri";
            }
        }
        // 携帯の場合などクッキーが使用できない環境ではセッションクエリーをURLに付加
        $sessionName = session_name();
        $sessionId = session_id();
        if (! isset($_COOKIE[$sessionName]) && $sessionId && isset($options['session']) && $options['session']) {
            // セッションクエリーが付いてれば消去
            //        $uri = preg_replace("/&*{$sessionName}=[^&]+/is", '', $uri);
            $uri = preg_replace("/([&\\?]){$sessionName}=[^&]?/is", '$1', $uri);
            $con = (strpos($uri, '?')) ? '&' : '?';
            $uri .= "{$con}{$sessionName}={$sessionId}";
            if (strlen($sessionId) != 32) {
                trigger_error('session key error' . $uri, E_USER_WARNING);
            }
        }
        //argsオプション
        if (isset($options['val'])) {
            $query = array('_cv' => $options['val']);
        } else {
            $query = '';
        }
        if (isset($options['click'])) {
            $click = array(
                BEAR_Page::KEY_CLICK_NAME => $options['click']
            );
            if (is_array($query)) {
                $query = array_merge($query, $click);
            } else {
                $query = array_merge(array('_sc' => $query), $click);
            }
        }
        if ($query) {
            $uri = $uri . '?' . http_build_query($query);
        }
        if (isset($options['sval'])) {
            $session = BEAR::dependency('BEAR_Session', 'session');
            /* @var $session BEAR_Session */
            $session->set('val', $options['sval']);
        }
        if (isset($options['permanent']) && $options['permanent']) {
            $this->setHeader('HTTP/1.1 301 Moved Permanently');
        }
        //　ロケーションヘッダー出力
        $this->setHeader("Location: {$uri}");
        $this->_log->log('redirect', $uri);
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
            if (! empty($headers[$header])) {
                return $headers[$header];
            }
        }

        return false;
    }

    /**
     * モバイル用のヘッダーをセット
     *
     * @param string $header
     */
    protected function setMobileHeader($header = 'text/html')
    {
        $this->_mobileHeader = $header;
    }
}
