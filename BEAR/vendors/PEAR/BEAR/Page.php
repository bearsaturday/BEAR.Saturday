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
 * @version   SVN: Release: $Id: Page.php 1260 2009-12-08 14:41:23Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Page/BEAR_Page.html
 */
/**
 * BEAR_Pageクラス
 *
 * <pre>
 * ページの抽象クラスです。onで始まるメソッドがイベントドリブンでコールされます。
 * が基本3メソッドです。
 *
 * onInit($args)        初期化
 * onOutput()           ページ出力処理
 * onAction($submit)    フォーム送信後の処理
 *
 * @category  BEAR
 * @package   BEAR_Page
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Page.php 1260 2009-12-08 14:41:23Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Page/BEAR_Page.html
 * @abstract
 */
abstract class BEAR_Page extends BEAR_Base
{

    /**
     * アクティブリンク・クリックネーム　クエリーキー
     *
     */
    const KEY_CLICK_NAME = '_cn';

    /**
     * アクティブリンク・クリックバリュ　クエリーキー
     *
     */
    const KEY_CLICK_VALUE = '_cv';

    /**
     * ページモード - HTML
     *
     * @var string
     */
    const CONFIG_MODE_HTML = 'html';

    /**
     * ページモード - リソース
     *
     * @var string
     */
    const CONFIG_MODE_RESOURCE = 'res';

    /**
     * onInit()等の引数
     *
     * @var array
     */
    protected $_args = array();

    /**
     * ページ実行ログ
     */
    protected $_pageLog = array();

    /**
     * Click名
     *
     * @var string
     */
    private $_onClick = null;

    /**
     * 文字コード変換の場合のモバイルの文字コード
     */
    private $_codeFromMoble;

    /**
     * onInit()でsetされた値
     *
     * @var array
     */
    private $_values = array();

    /**
     * ページキャッシュ
     *
     * @var string
     */
    private $_cache = array('use_cache' => false, 'headers' => null, 'html' => null);

    /**
     * AJAXコマンドデータ
     *
     * @var array
     */
    private $_ajax = array();

    /**
     * charset
     *
     * マルチエージェントの場合のcharset
     */
    private static $_charset = null;

    /**
     * クリックをゲット
     *
     * @return string
     */
    public function getOnClick()
    {
        return $this->_onClick;
    }

    /**
     * UAコード
     *
     * @var string
     */
    protected $_ua = BEAR_Agent::UA_DEFAULT;

    /**
     * セッション
     *
     * @var BEAR_Session
     */
    protected $_session;

    /**
     * リソースアクセス
     *
     * @var BEAR_Resource
     */
    protected $_resource;

    /**
     * View
     *
     * @var mixed
     */
    protected $_view = 'view';

    /**
     * ページリソースモード
     *
     * @var bool
     */
    private static $_isResourceMode = false;

    /**
     * クリックをセット
     *
     * @param string $onClick
     */
    public function setOnClick($onClick)
    {
        $this->_onClick = $onClick;
    }

    /**
     * コンストラクタ
     *
     * <pre>
     * BEAR_MainからのUA情報があればPageにセットします。
     * </pre>
     *
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        if (isset($config['BEAR_Main']['ua'])) {
            $this->_config['ua'] = $config['BEAR_Main']['ua'];
        }
    }

    /**
     *　インジェクト
     *
     * @retun void
     */
    public function onInject()
    {
        $this->header = BEAR::dependency('BEAR_Page_Header');
        $this->_view = $this->_config;
    }

    /**
     * ページ初期化ハンドラ
     *
     * ページの初期化を行います。onOutput()で出力させる変数を全て用意しsetします。<br/>
     * setした変数はキャッシュ可能です。
     *
     * @param args $args ページ引数
     *
     * @return void
     */
    public function onInit(array $args)
    {
    }

    /**
     * ページ表示ハンドラ
     *
     * <pre>
     * onInit()で実行された後、フォーム送信されてない、
     * またはバリデーションNGのときにコールされます。
     * </pre>
     *
     * @return void
     */
    public function onOutput()
    {
    }

    /**
     * バリデーションOKハンドラ
     *
     * <pre>フォーム送信されバリデーションOKの場合にonInit()の後にコールされます</pre>
     *
     * @param mixed $submit フォーム内容
     *
     * @return void
     *
     */
    public function onAction(array $submit)
    {
    }

    /**
     * HTML表示
     *
     * <pre>
     * ViewでHTML表示をします。ViewはデフォルトではBEAR_View_Smartyです。
     * BEAR_View_Interfaceを実装したクラスにインジェクトを使って置き換える事ができます。
     * </pre>
     *
     * @param $tplName
     *
     * @return void
     */
    public function display($tplName = null)
    {
        $this->_viewAdaptor()->display($tplName);
    }

    /**
     * HTML文字列取得
     *
     * <pre>
     * BEAR_Page::displayと違いHTML出力の代わりに文字列を取得します。
     * </pre>
     *
     * @param string $tplName テンプレートファイル名
     *
     * @return   mixed
     */
    public function fetch($tplName = null)
    {
        return $this->_viewAdaptor()->fetch($tplName);
    }

    /**
     * Viewアダプター
     *
     * <pre>
     * Viewにページバリューをアサインして、Viewを返します。
     * 受け取ったクラインとではfetchかdisplayが利用可能です。
     * UAスニッフィングがtrueならエージェント
     * </pre>
     *
     * @return void
     */
    protected function _viewAdaptor()
    {
    	$config = $this->_config;
    	$uaSniffing = isset($this->_config['BEAR_Main']['enable_ua_sniffing']) ? $this->_config['BEAR_Main']['enable_ua_sniffing'] : false;
        if (isset($this->_config['BEAR_Main']['enable_ua_sniffing']) && $this->_config['BEAR_Main']['enable_ua_sniffing'] === true) {
            $adaptor = BEAR::dependency('BEAR_Agent_Adaptor_' . $this->_config['ua']);
            $agentConfig = $adaptor->getConfig();
            $config['agent_config'] = $agentConfig;
            $config['enable_ua_snig'] = $agentConfig;
        } else {
            $options = array();
        }
        $config['adaptor'] = isset($this->_config['view']) ? $this->_config['view'] : '';
        $config['values'] = $this->_values;
        $config['ua_sniffing'] = $uaSniffing;
        $this->_view = BEAR::dependency('BEAR_View', $config);
        return $this->_view;
    }

    /**
     * 値をセット
     *
     * <pre>
     * outputでdisplay()やoutput()とする値をセットします。
     * </pre>
     *
     * @param mixed $key    キー string
     * @param mixed $value  値
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->_values[$key] = $value;
    }

    public function setAll($values)
    {
        $this->_values = $values;
    }

    /**
     * ページ変数取得
     */
    public function get($key = null)
    {
        $result = (is_null($key)) ? $this->_values : $this->_values[$key];
        return $result;
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
        $this->header->setHeader($header);
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
        $this->header->flushHeader();
    }

    /**
     * HTTP出力
     *
     * 指定されたフォーマットでHTTP出力します。
     * 指定フォーマットのアウトプットファイルを以下の順（BEAR, App)で探します。
     *
     * 1) /BEAR/Resource/output/
     * 2) /App/output/
     *
     * @param string $format  フォーマット
     * @param mixed  $options オプション
     *
     * @return void
     */
    public function output($format = 'print', array $options = array())
    {
        if  (file_exists(_BEAR_APP_HOME . '/App/Resource/output/' . $format . '.php')) {
            $formatFile = _BEAR_APP_HOME . '/App/Resource/output/' . $format . '.php';
        } elseif (file_exists(_BEAR_BEAR_HOME . '/BEAR/Resource/output/' . $format . '.php')) {
            $formatFile = _BEAR_BEAR_HOME . '/BEAR/Resource/output/' . $format . '.php';
        } else {
            $ro = BEAR::factory('BEAR_Ro');
            $ro->setCode(BEAR::CODE_BAD_REQUEST);
            $ro->httpOutput();
        }
        include_once $formatFile;
        if (!function_exists('output' . $format)) {
            $info = array('format' => $format);
            $msg = 'Output format is unavailable.（アウトプットフォーマットが利用できません)';
            throw $this->_exception('Bad Page Request', array(
                'info' => $info));
        }
        $ro = call_user_func('output' . $format, $this->_values, $options);
        /* @var $ro BEAR_Ro */
        $ro->outputHttp();
        exit();
    }

    /**
     * ページ引数への変数インジェクト
     *
     * @param string $key     ページ引数キー
     * @param mixed  $val     ページ引数にインジェクトする値
     * @param mixed  $default デフォルト
     */
    public function injectArg($key, $val, $default = null)
    {
        $this->_args[$key] = !is_null($val) ? $val : $default;
    }

    /**
     * GETをインジェクト
     *
     * @param string $key
     * @param string $getKey
     *
     * @return void
     */
    public function injectGet($key, $getKey = null, $default = null){
        $getKey = $getKey ? $getKey : $key;
        if (isset($_GET[$getKey])) {
            $this->_args[$key] = $_GET[$getKey];
        } elseif ($default) {
            $this->_args[$key] = $default;
        }
    }

    /**
     * ページ引数へ連想配列でインジェクト
     *
     * @param array $args 引数全部
     */
    public function injectArgs($args)
    {
        $args = (array)BEAR::loadValues($args);
        $this->_args = array_merge($this->_args, $args);
    }

    public function injectAjaxRequest()
    {
        $ajax = BEAR::dependency('BEAR_Page_Ajax');
        /** @param $ajax BEAR_Page_Ajax */
        $ajaxReq = $ajax->getAjaxRequest();
        $this->_args = array_merge($this->_args, $ajaxReq);
    }

    public function injectAjaxValues()
    {
        $ajax = BEAR::dependency('BEAR_Page_Ajax');
        /** @param $ajax BEAR_Page_Ajax */
        $ajaxReq = $ajax->getAjaxRequest();
        $this->_args = array_merge($this->_args, $ajaxReq);
    }

    /**
     * ページ引数の取得
     *
     * @return array ページ引数
     */
    public function getArgs()
    {
        return $this->_args;
    }

    /**
     * ページ終了
     *
     * <pre>
     * ページを途中で終了します。コードとメッセージを指定すると指定HTTPコードのヘッダーと出力画面で終了します。
     * </pre>
     * s
     * @param int    $httpCode HTTPコード
     * @param string $msg      HTTPコードメッセージ(200以外）
     *
     * @return void
     */
    public function end($httpCode = 200, $msg = 'Error')
    {
        if ($httpCode === 200) {
            $main = BEAR::dependency('App_Main');
            $main->end();
        } else {
            ob_clean();
            throw new Panda_Exception($msg, $httpCode);
        }
    }

    /**
     * setした値をすべて取得
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * ページキャッシュのキーを生成
     *
     * @return mixed (bool)falseキャッシュ不可 | (string)キャッシュキー
     *
     * @todu UA
     */
    public function getCacheKey()
    {
        static $result = null;

        // キーの同一性を保障＆パフォーマンス
        if (!is_null($result)) {
            return $result;
        }
        $ua = (isset($this->_config['ua'])) ? $this->_config['ua'] : '';
        $pageConfig = $ua . serialize(array($this->getArgs(), $this->_config));
        $pagerKey = isset($_GET['_start']) ? $_GET['_start'] : '';
        $result = get_class($this) . '-' . $pagerKey . '-' . ($pageConfig) ;
        return $result;
    }

    /**
     *
     * @return unknown_type
     */
    public function clearPageCache(){
        $key = $this->getCacheKey();
        $cache = BEAR::dependency('BEAR_Cache');
        $cache->delete($key);
    }
}
