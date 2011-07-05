<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Page
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Page.php 2499 2011-06-11 10:04:36Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * Page
 *
 * ページの抽象クラスです。onで始まるメソッドがイベントに応じて呼び出されます。
 *
 *<ul>
 * <li>onClick(array $args)      クリック</li>
 * <li>onInit(array $args)       初期化</li>
 * <li>onOutput()                ページ出力処理</li>
 * <li>onAction(array $submit)   フォーム送信後の処理</li>
 * <li>onException(Exception $e) 例外</li>
 * </ul>
 *
 * @category  BEAR
 * @package   BEAR_Page
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Page.php 2499 2011-06-11 10:04:36Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
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
    const CONFIG_MODE_RESOURCE = 'resource';

    /**
     * ヘッダーオブジェクト
     *
     * @var BEAR_Page_Header
     */
    public $header;

    /**
     * Pageリソースヘッダー
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * Pageリソースボディ
     *
     * @var string
     */
    protected $_body = null;

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
     *  ページにセットされたバリュー
     *
     * @var array
     */
    private $_values = array();

    /**
     * ページにセットされたリソース
     *
     * @var array
     */
    protected $_ro = array();

    /**
     * 出力されたページをリソースオブジェクトにしたもの
     *
     * @var array
     */
    protected $_pageRo = array();

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
     * @param string $onClick クリック名
     *
     * @return void
     */
    public function setOnClick($onClick)
    {
        $this->_onClick = $onClick;
    }

    /**
     * Constructor
     *
     * BEAR_MainからのUA情報があればPageにセットします。
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     *　インジェクト
     *
     * @return void
     */
    public function onInject()
    {
        $this->header = BEAR::dependency('BEAR_Page_Header');
        $this->_view = $this->_config;
    }

    /**
     * ページハンドラ
     *
     * <pre>
     * onInject()で注入された$argsやプロパティを利用して
     * メソッド内でリソースリクエストを行いviewにsetします。
     * </pre>
     *
     * @param array $args ページ引数
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
     * onInit()でページにsetされた値をこのメソッド内で出力します。
     * このメソッドはフォームのバリデーションが行われ、その結果が全てOKだった時のみ_呼び出されません_
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
     * <pre>
     * フォーム送信されバリデーションOKの場合にonInit()の後にコールされます。
     * $submitはQuickFormに追加されバリデーションされた値のうち最初のエレメントが_でないもののみが渡されます。
     * 例えば0か1のみがセレクト可能なエレメントがあっととして、ルールを追加しなくても
     * "2"がサブミットされた場合には$submitに渡されません。
     * </pre>
     *
     * @param array $submit フォーム内容
     *
     * @return void
     */
    public function onAction(array $submit)
    {
    }

    /**
     * HTML表示
     *
     * onInit()でセットされたリソース結果をHTML出力します。
     *
     * @param string $tplName テンプレート名
     * @param array  $options オプション
     *
     * @return void
     * @internal  $this->_config['mode']がself::CONFIG_MODE_HTMLでないときはunit test用にHTTP出力されません。
     */
    public function display($tplName = null, array $options = array())
    {
        if (BEAR::exists('pager')) {
            $pager = (array)BEAR::get('pager');
            $this->set('pager', $pager);
        }
        $this->_pageRo = $this->_viewAdapter()->display($tplName, $options);
        $this->_outputHttp($this->_pageRo);
    }

    /**
     * ページをリソースオブジェクトとして取得
     *
     * <pre>
     * モードに応じてページをリソースとして取得します。
     *
     * $this->_config['mode']
     *
     * 'resource'
     *   $this->set()でアサインされたリソース値がリソースボディになります。list<setKey, rosourceBody>の形式です
     * 'page'
     *   $this->display()で出力されるHTTP（ヘッダー、ボディー）がリソースのヘッダー、ボディーになります。
     *
     * @return BEAR_Ro
     */
    public function getPageRo()
    {
        return $this->_pageRo;
    }

    /**
     * HTML文字列取得
     *
     * BEAR_Page::displayと違いHTML出力の代わりに文字列を取得します。
     *
     * @param string $tplName テンプレートファイル名
     *
     * @return string
     */
    public function fetch($tplName = null)
    {
        return $this->_viewAdapter()->fetch($tplName);
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
     * @return BEAR_View_Adapter
     */
    protected function _viewAdapter()
    {
        $config = $this->_config;
        if (isset($this->_config['enable_ua_sniffing']) && $this->_config['enable_ua_sniffing'] === true) {
            $adapter = BEAR::dependency('BEAR_Agent_Adapter_' . $this->_config['ua'], array('ua' => $this->_config['ua']));
            $agentConfig = $adapter->getConfig();
            $config['agent_config'] = $agentConfig;
            $config['enable_ua_sniffing'] = true;
        } else {
            $config = array();
            $config['enable_ua_sniffing'] = false;
            $config['ua'] = 'Default';
        }
        $config['values'] = $this->_values;
        $config['ro'] = $this->_ro;
        $config['resource_id'] = $this->_config['resource_id'];
        $this->_view = BEAR::factory('BEAR_View', $config);
        return $this->_view;
    }

    /**
     * ページバリューをセット
     *
     * @param mixed $key   キー string
     * @param mixed $value 値
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->_values = array_merge($this->_values, $key);
        } else {
            $this->_values[$key] = $value;
        }
    }

    /**
     * ページにリソースをセット
     *
     * @param string  $key リソース名
     * @param BEAR_Ro $ro  リソースオブジェクト
     *
     * @return void
     */
    public function setRo($key, BEAR_Ro $ro)
    {
        $this->_ro[$key] = $ro;
    }

    /**
     * ページ変数取得
     *
     * @param string $key 変数キー
     *
     * @return mixed
     *
     * @deprecated
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
     * @param array  $options オプション
     *
     * @return void
     * @throws BEAR_Page_Exception
     */
    public function output($format = 'print', array $options = array())
    {
        $isValid = true;

        if (file_exists(_BEAR_APP_HOME . '/App/Resource/output/' . $format . '.php')) {
            $formatFile = _BEAR_APP_HOME . '/App/Resource/output/' . $format . '.php';
        } elseif (file_exists(_BEAR_BEAR_HOME . '/BEAR/Resource/output/' . $format . '.php')) {
            $formatFile = _BEAR_BEAR_HOME . '/BEAR/Resource/output/' . $format . '.php';
        } else {
            $isValid = false;
        }
        include_once $formatFile;
        if (!$isValid || !function_exists('output' . $format)) {
            $info = array('format' => $format);
            $msg = 'Output format is unavailable.（アウトプットフォーマットが利用できません)';
            throw $this->_exception('Invalid output format', array('info' => $info));
        }
        $ro = call_user_func('output' . $format, $this->_values, $options);
        $this->_outputHttp($ro);
    }

    /**
     * ページリソースをHTTP出力します
     *
     * @param BEAR_Ro $ro
     *
     * @return void
     */
    protected function _outputHttp(BEAR_Ro $ro)
    {
        if ($this->_config['mode'] === self::CONFIG_MODE_HTML) {
           $ro->outputHttp();
        }
    }

    /**
     * ページ引数への変数インジェクト
     *
     * @param string $key     ページ引数キー
     * @param mixed  $val     ページ引数にインジェクトする値
     * @param mixed  $default デフォルト
     *
     * @return void
     */
    public function injectArg($key, $val, $default = null)
    {
        $this->_args[$key] = !is_null($val) ? $val : $default;
    }

    /**
     * $GETをインジェクト
     *
     * URLクエリーの$_GETをonInit($args)の$argsにインジェクトします。
     *
     * Injectするキー
     * @param string $getKey  $_GETキー
     * @param mixed  $default デフォルト
     *
     * @return void
     */
    public function injectGet($key, $getKey = null, $default = null)
    {
        $getKey = $getKey ? $getKey : $key;
        if (isset($_GET[$getKey])) {
            $this->_args[$key] = $_GET[$getKey];
        } elseif (isset($default)) {
            $this->_args[$key] = $default;
        }
    }

    /**
     * ページ引数へ連想配列でインジェクト
     *
     * @param array $args 引数全部
     *
     * @return void
     */
    public function injectArgs($args)
    {
        $args = (array)BEAR::loadValues($args);
        $this->_args = array_merge($this->_args, $args);
    }

    /**
     * AJAXリクエストの値をインジェクト
     *
     * AJAXリクエストの値をonInit($args)の$argsにインジェクトします。
     *
     * @return void
     */
    public function injectAjaxValues()
    {
        $ajax = BEAR::dependency('BEAR_Page_Ajax');
        /* @param $ajax BEAR_Page_Ajax */
        $ajaxReq = $ajax->getAjaxRequest();
        $this->_args = array_merge($this->_args, $ajaxReq);
    }

    /**
     * AJAXリクエストの値をインジェクト
     *
     * @deprecated
     * @ignored
     * @return void
     */
    public function injectAjaxRequest()
    {
        $this->injectAjaxValues();
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
     * ページを途中で終了します。コードとメッセージを指定すると指定HTTPコードのヘッダーと出力画面で終了します。
     *
     * @param int    $httpCode HTTPコード
     * @param string $msg      HTTPコードメッセージ(200以外）
     *
     * @return void
     * @throws Panda_Exception
     */
    public function end($httpCode = 200, $msg = 'Error')
    {
        if ($httpCode === 200) {
            BEAR::dependency('App_Main')->end();
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
     * setしたRoを全て取得
     *
     * @return array
     */
    public function getRo()
    {
        return $this->_ro;
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
        $sortKey = isset($_GET['_sort']) ? $_GET['_sort'] : '';
        $result = get_class($this) . '-' . $pagerKey . '-'  . $sortKey . '-' . $pageConfig;
        return $result;
    }

    /**
     * ページキャッシュクリア
     *
     * @return void
     */
    public function clearPageCache()
    {
        $key = $this->getCacheKey();
        $cache = BEAR::dependency('BEAR_Cache');
        $cache->delete($key);
    }

    /**
     * プロトタイプリソースをページバリューにセット
     *
     * リソースのsetでスタックに積まれた複数のプロトタイプリソースを取り出しページにsetします。
     *
     * 'lazy' lazy Roとしてsetします。viewで出現したタイミングで実リソースリクエストが行われます。
     * 'object' roオブジェクトとしてsetされます。
     * 'value' 変数（多くの場合連想配列）としてsetされます。
     *
     * @todo ajaxオプション実装
     *
     * @return void
     */
    public function setPrototypeRo()
    {
        static $_registerResourceOnShutdown = false;
        // initでsetされたroをバリューに
        $stackedRos = BEAR::dependency('BEAR_Ro_Prototype')->popAll();
        foreach ($stackedRos as $item) {
            list($key, $prototypeRo) = each($item);
            /* @var $prototypeRo BEAR_Ro_Prototype */
            $setOption = $prototypeRo->getSetOption();
            $config = $prototypeRo->getConfig();
            $options = $config['request']['options'];
            switch ($setOption) {
                case  'ajax':
                    $prototypeRo->setConfig('is_ajax_set', true);
                case  'lazy':
                    $this->set($key, $prototypeRo);
                    break;
                case  'object':
                    $ro = $prototypeRo->request();
                    $this->set($key, $ro);
                    break;
                case  'shutdown':
                    if ($_registerResourceOnShutdown === false) {
                        $_registerResourceOnShutdown = true;
                        register_shutdown_function(array('BEAR_Ro_Shutdown', 'onShutdown'));
                    }
                    BEAR::dependency('BEAR_Ro_Shutdown')->set($prototypeRo);
                    break;
                case  'value':
                default:
                    $value = $prototypeRo->getValue();
                    $this->set($key, $value);
            }
        }
    }

}
