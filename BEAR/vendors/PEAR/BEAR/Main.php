<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Main
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Main.php 1310 2010-01-04 08:04:06Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Main/BEAR_Main.html
 */
/**
 * メインクラス
 *
 * <pre>
 * ページオブジェクトを実行するクラスです。。
 * ページに実装されたイベントハンドラをイベント毎にコールします。
 * キャッシュオプションでページの初期化（onInit）をキャッシュするinitキャッシュ、
 * テンプレート生成までも含めたページキャッシュのキャッシュオプションを
 * 指定することができます。
 *
 * Example 1.キャッシュページの実行
 * </pre>
 * <code>
 * class Blog_RSS extends App_Page{
 * }
 * $config = array('page_cache'=>'init', 'life'=>60);
 * new BEAR_Main('Blog_RSS', $config);
 * //10分間のページキャッシュ
 * </code>
 *
 * @category  BEAR
 * @package   BEAR_Main
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Main.php 1310 2010-01-04 08:04:06Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Main/BEAR_Main.html
 */
class BEAR_Main extends BEAR_Base
{

    /**
     * runできる
     *
     * @var bool
     */
    private static $_isRunnable = true;

    /**
     * ページインスタンス
     *
     * @var object App_Page
     */
    private $_page = null;

    /**
     * ページ引数
     *
     * ページの$_GETや$_COOKIE,CLIの引数。
     *
     * @var array
     */
    private $_args = array();

    /**
     * サブミット値
     *
     * $_POSTか$_GETの内容が入ります
     * BEAR_Page::queryMethodプロパティの設定に依存します
     *
     * @var mixed
     * @access private
     */
    private $_submit = false;

    /**
     * Ajaxサブミットか
     *
     * @var bool
     */
    private $isAjaxSubmit = false;

    /**
     * コンストラクタ
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * インジェクト
     *
     * @return void
     */
    public function onInject()
    {
        // ページを生成してレジストリにセット
        $config = array('BEAR_Main'=>$this->_config, 'resource_id'=>$this->_config['page_class'], 'mode'=>BEAR_Page::CONFIG_MODE_HTML);
        $options = array('injector'=> (isset($this->_config['injector']) ? $this->_config['injector'] : 'onInject'));
        $this->_page = BEAR::factory($this->_config['page_class'], $config, $options);
        BEAR::set('page', $this->_page);
    }

    /**
     * ページクラス実行
     *
     * 指定されたページクラスをインスタンス化し実行します。
     *
     * @param string $pageClass ページクラス名
     * @param array  $config    設定
     *
     * @return void
     */
    public static function run($pageClass, array $config = array())
    {
        // include
        if (self::$_isRunnable === false) {
            $log = BEAR::dependency('BEAR_Log');
            $log->log("Page Include", $pageClass);
            return;
        }
        self::$_isRunnable = false;
        // ページクラス存在チェック
        if (!$pageClass || !class_exists($pageClass, false)) {
            $info = array('page_class' => $pageClass);
            throw new BEAR_Page_Exception('Page class is not defined.（ページクラスが定義されていません)', array(
                'info' => $info));
        }
        // フォーム初期化
        if (class_exists('BEAR_Form', false)) {
            BEAR_Form::init();
        }
        // メイン実行
        $config['page_class'] = $pageClass;
        try {
            $main = BEAR::dependency('App_Main', $config);
            $main->_run();
        } catch(BEAR_Exception $e) {
            $redirect = $e->getRedirect();
            if (!is_null($redirect)) {
                if ($redirect === false) {
                    $main->end();
                } else {
                    $header = BEAR::dependency('BEAR_Page_Header');
                    $header->redirect($e->getRedirect());
                }
            }
            $log = BEAR::dependency('BEAR_Log');
            $log->log("BEAR_Page_Exception", $e);
            if (isset($main->_page) && method_exists($main->_page, 'onException')) {
                $main->_page->onException($e);
            } else {
                throw $e;
            }
        } catch(Exception $e) {
            throw $e;
        }
    }

    /**
     * ページ開始
     *
     * ページを実行します。
     *
     * @return void
     */
    private function _run()
    {
        $this->_sessionStart();
    	// debugログ
        if ($this->_config['debug']) {
            $this->_runDebug();
            $this->_log->log('Page', $this->_config);
        }
        // init/ページキャッシュ
        $initCache = $this->_runCache();
        // args取取
        $args = $this->_page->getArgs();
        // onClick
        $this->_runClick($args);
        // onInit
        if ($initCache === false) {
            $this->_log->log('onInit', $args);
            $this->_runInit($args);
        } else {
            $this->_page->setAll($initCache);
        }
        // submit ?
        $hasSubmit = (isset($_POST['_token']) || isset($_GET['_token'])) ? true : false;
        if ($hasSubmit !== true) {
            // onOutput()
            $this->_runPreOnOutput();
            $this->_page->onOutput();
        } else {
            // onAction(OK) or onOutput()(NG)
            $this->_runSubmit();
        }
        $this->end();
    }

    /**
     * Submit処理
     *
     * <pre>
     * フォームがサブミットされた場合の処理を行います。
     * サブミットされたらバリデーションを自動で行いOKならPage::onAction(), NGならPage::onOutput()をコールします。
     * </pre>
     *
     * @return void
     */
    private function _runSubmit()
    {
        $this->_submit = isset($_POST['_token']) ? $_POST : $_GET;
        // form作成
        $formName = BEAR_Form::getSubmitFormName($this->_submit);
        try {
            $form = BEAR::get('BEAR_Form_' . $formName);
        } catch(Exception $e) {
            $this->_log->log('BEAR_Form Exception', $e->__toString());
            $this->_runPreOnOutput();
            $this->_page->onOutput();
            $this->end();
        }
        // submitバリデーション
        $isValidate = $form->validate();
        $this->_isAjaxSubmit = isset($_SERVER['HTTP_X_BEAR_AJAX_REQUEST']);
        if ($isValidate) {
            // submit OK
            $this->_formValidationOk($form, $formName);
        } else {
            // submit NG
            $this->_log->log('Submit NG', array(
                'Rules' => $form->_rules,
                'Errors' => $form->_errors));
            if ($this->_isAjaxSubmit) {
                // AJAXバリデーションNG]
                $this->_ajaxValidationNG($form);
            } else {
                $this->_runPreOnOutput();
                $this->_page->onOutput();
            }
        }
    }

    /**
     * deubg実行
     *
     * @return void
     */
    private function _runDebug()
    {
        // デバック用キャッシュクリア
        if (isset($_GET['_cc'])) {
            BEAR_Util::clearAllCache();
            $this->exitMain();
        }
        // log
        $log = array();
        $log['BEAR'] = BEAR::VERSION;
        $log['URI'] = $_SERVER['REQUEST_URI'];
        $log['time'] = _BEAR_DATETIME;
        $this->_log->log('start', $log);
    }

    /**
     * onClickコール
     *
     * @return void
     */
    private function _runClick(array $args)
    {
        // onClick
        $isActiveLink = isset($_GET[BEAR_Page::KEY_CLICK_NAME]);
        $hasMethod = isset($_GET[BEAR_Page::KEY_CLICK_NAME]) && method_exists($this->_page, $onClickMethod = 'onClick' . $_GET[BEAR_Page::KEY_CLICK_NAME]);
        if ($isActiveLink && $hasMethod) {
            $this->_page->setOnClick($_GET[BEAR_Page::KEY_CLICK_NAME]);
            $args['click'] = isset($_GET[BEAR_Page::KEY_CLICK_VALUE]) ? $_GET[BEAR_Page::KEY_CLICK_VALUE] : null;
            $this->_log->log('onClick', array(
                'click' => $this->_page->getOnClick(),
                'args' => $args));
            $this->_page->$onClickMethod($args);
        } elseif ((!isset($_GET[BEAR_Page::KEY_CLICK_NAME]) && method_exists($this->_page, 'onClickNone'))) {
            $this->_page->onClickNone($args);
        }
    }

    /**
     * onInitコール
     *
     * @return void
     */
    private function _runInit(array $args)
    {
        $config['class'] = get_class($this->_page);
        $config['method'] = 'onInit';
        $annotation = BEAR::factory('BEAR_Annotation', $config);
        //        // requireアノテーション (引数のチェック)
        try {
            $annotation->required($args);
        } catch (BEAR_Annotation_Exception $e) {
            if (method_exists($this->_page, 'onException')) {
                $this->_page->onException($e);
            } else {
                throw new Panda_Exception('Bad Request', 400, array('info' => $e->getInfo()));
            }
        }
        $this->_page->onInit($args);
    }

    /**
     * 出力前のバッファの消去
     *
     * debugモード時はdebug出力エリアとして出力します。
     *
     * @return void
     */
    private function _runPreOnOutput()
    {
        $app = BEAR::get('app');
        $buff = ob_get_contents();
        ob_clean();
        ob_start();
        if ($this->_config['debug'] && $buff) {
            $ajax = BEAR::dependency('BEAR_Page_Ajax');
            if (!$ajax->isAjaxRequest()) {
                echo '<div style="border-style: dotted;">' . $buff . '</div>';
            } else {
                //                p($buff, 'fire');
            }
        }
    }

    /**
     * セッションスタート
     *
     * @return void
     */
    protected function _sessionStart()
    {
    	$app = BEAR::get('app');
        // セッションスタート
        if (isset($this->_config['enable_ua_sniffing']) && $this->_config['enable_ua_sniffing'] === true) {
            $adaptorConfig = $this->_agent->adaptor->getConfig();
//            ini_set('session.use_trans_sid', $adaptorConfig['session_trans_sid']);
            if ($adaptorConfig['enable_session'] && $app['BEAR_Session']['adaptor'] != 0) {
                BEAR::dependency('BEAR_Session')->start();
            }
        } elseif ($app['BEAR_Session']['adaptor'] != 0) {
            BEAR::dependency('BEAR_Session')->start();
        }

    }
    /**
     * ページ終了処理
     *
     * <pre>
     * ヘッダーとコンテンツを出力して終了します。
     * </pre>
     *
     * @return void
     */
    public function end()
    {
        $body = ob_get_contents();
        // ページキャッシュ書き込み
        if (isset($this->_config['cache']['type'])) {
            if ($this->_config['cache']['type'] === 'page') {
                $cacheData = array('type' => 'page', 'headers' => headers_list(), 'body' => $body);
                $this->_writeCache($cacheData);
            } elseif ($this->_config['cache']['type'] === 'init') {
                $cacheData = array('type' => 'init', 'init' => $this->_page->get());
                $this->_writeCache($cacheData);
            } else {
                throw new $this->_expection('Invalid Cache Type', $this->_config);
            }
        }
        if ($this->_config['debug']) {
            $body = BEAR_Dev_Util::onOutpuHtmlDebug($body);
            ob_clean();
            echo $body;
        }
        // ヘッダー出力
        if (isset($this->_page->header) && $this->_page->header instanceof BEAR_Page_Header){
            $this->_page->header->flushHeader();
        }
        // 終了
        $this->exitMain();
    }

    /**
     * init/ページキャッシュの読み込み
     *
     * <pre>
     * ページキャッシュが存在すれば表示して終了します。initキャッシュなら配列を返します。
     * キャッシュを書き込むときはtrueを返します。
     * ページキャッシュはヘッダーもキャッシュされます。
     * </pre>
     *
     * @return mixed array (bool)false no cache | (bool) true write cache | (array) initcache
     */
    private function _runCache()
    {
        //キャッシュ初期化
        $type = isset($this->_config['cache']['type']) && $this->_config['cache']['type'];
        if (!$type) {
            return false;
        }
        $cache = BEAR::dependency('BEAR_Cache');
        // ライフ設定
        assert(is_int($this->_config['cache']['life']));
        $cache->setLife($this->_config['cache']['life']);
        // キーの生成（内部で保持）
        $key = $this->_page->getCacheKey();
        if (!$type || isset($_GET['_token']) || (isset($_POST['_token'])) || (isset($_GET['_cn']))) {
            return false;
        }
        $cacheData = $cache->get($key);
        if (!$cacheData) {
            $this->_log->log('Page/Init Cache[No hit]', $key);
            // write lazy cache
            return false;
        } elseif ($cacheData['type'] === 'page') {
            // read page cache
            $this->_log->log('Page Cache[R]', $key);
            if (is_array($cacheData['headers'])) {
                foreach ($cacheData['headers'] as $header) {
                    header($header);
                }
            }
            //            $this->_page->flushHeader();
            echo $cacheData['body'];
            $this->exitMain();
        } else {
            // read init cache
            return $cacheData['init'];
        }
    }

    /**
     * ページキャッシュ書き込み
     *
     * ヘッダーとコンテンツをキャッシュに保存
     *
     * @param string $cacheData キャッシュ
     *
     * @return void
     */
    private function _writeCache($cacheData)
    {
        $cache = BEAR::dependency('BEAR_Cache');
        $cache->setLife($this->_config['cache']['life']);
        $key = $this->_page->getCacheKey();
        $cache->set($key, $cacheData);
        $this->_log->log('Main Cache[W]', $this->_config['cache']);
    }

    /**
     * TokenがAJAXのものか検査
     *
     * @param string $token トークン
     *
     * @return bool
     *
     *
     * @ignore
     */
    private function _isAjaxToken($token)
    {
        $ajaxToken = BEAR_Form::makeToken(true);
        $result = ($token == $ajaxToken) ? true : false;
        return $result;
    }

    /**
     * トークン有効チェック
     *
     * セッショントークンが有効なものかどうか検査します。
     *
     * @param string $token トークン
     *
     * @return bool
     */
    function isTokenValid($token)
    {
        $mdFiveShort = substr($token, 1, 12);
        $tokenCheckSum = substr($token, 13, 2);
        $genuineCheckSum = substr(md5(hexdec($mdFiveShort) * 5 - 1), 0, 2);
        $result = ($tokenCheckSum == $genuineCheckSum) ? true : false;
        $this->_log->log('Token Status', $result ? "Secure" : "Unsecure");
        return $result;
    }

    /**
     * フォームバリデーションOK処理
     *
     * トークンの検査を行い不正アクセスでなければonActionメソッドの引数に
     * POSTされたデータを与えてコールする。
     *
     * @param object $form     フォーム
     * @param object $formName フォーム名
     *
     * @return void
     */
    private function _formValidationOk($form, $formName = null)
    {
        $submit = $form->exportValues();
        BEAR_Form::$submitValue = $form->getSubmitValues();
        /**
         * HTML_QuickForm::exportValues()
         *
         * getSubmitValues()  とは異なり、この関数は フォームに追加された
         * 要素に対応する値で実際に送信されたもののみを返します。
         * 'man'/'woman' を選択するラジオボタンがあった場合、
         * 'other'は有効な送信値とはみなされません。
         * また、このメソッドでは file 要素の値を取得することもできません。
         */
        // アンダースコア始まりのsubmitを消去
        foreach ($submit as $submitKey => $value) {
            if (substr($submitKey, 0, 1) == '_') {
                unset($submit[$submitKey]);
                BEAR_Form::$submitHeader[$submitKey] = $value;
            }
            if ($value === null) {
                $submit[$submitKey] = '';
            }
        }
        // アクションコール
        $this->_page->onAction($submit);
        //追加でアクションコール
        $methodExists = method_exists($this->_page, 'onAction' . $formName);
        if ($methodExists) {
            // onAction.フォーム名() コール
            $actionMethodName = 'onAction' . $formName;
            $this->_page->$actionMethodName($submit);
        }
    }

    /**
     * Main終了
     *
     * @return void
     */
    public function exitMain()
    {
        exit();
    }

    /**
     * ヘッダー出力
     *
     * <pre>
     * ページキャッシュにも利用されます。
     * </pre>
     *
     * @return  void
     * @static
     *
     */
    public function _outputHeaders()
    {
        static $hasOut = false;
        $headers = $this->_page->getHeaders();
        if ($headers && !$hasOut) {
            //ヘッダー出力
            foreach ($headers as $header) {
                header($header, true);
            }
            $hasOut = true;
        } else {
            throw $this->_exception('Header is already out');
        }
    }

    /**
     * AJAXフォームでバリデーションNG
     *
     * エラーフォームエレメント名とエラーメッセージの連想配列をJSONで返す
     *
     * @param object $form フォームオブジェクト
     *
     * @return void
     */
    private function _ajaxValidationNG($form)
    {
        foreach ($form->_rules as $key => $value) {
            $ruleKeys[] = $key;
        }
        $ajaxErrorResult = array(
            'quickform' => array('form_id' => $form->_attributes['id'],
                'rules' => $ruleKeys,
                'errors' => $form->_errors));
        $this->_log->log('AJAX Form NG', $ajaxErrorResult);
        $formResult = array('validate' => false,
            'id' => $form->_attributes['id'],
            'errors' => $form->_errors);
        $ajax = BEAR::dependency('BEAR_Page_Ajax');
        $ajax->addAjax('quickform', $formResult);
        $this->_page->output('ajax');
        $this->end();
    }

    /**
     * AJAXフォームでバリデーションOK
     *
     * フォームエレメント名をJSONで返す
     *
     * @param object $form フォームオブジェクト
     *
     * @return void
     * @ignore
     */
    private function _ajaxValidationOk($form)
    {
        // ルール
        foreach ($form->_rules as $key => $value) {
            $ruleKeys[] = $key;
        }
        BEAR_Page::$formElement = array(
            'quickform' => array('form_id' => $form->_attributes['id'],
                'rules' => $ruleKeys));
    }

    /**
     * エラーの出力フォーマット(CLI or rich HTML)
     *
     * @return bool
     */
    public static function isCliErrorOutput()
    {
        static $result = null;

        if (is_null($result)) {
            $ajax = BEAR::dependency('BEAR_Page_Ajax');
            $result = $ajax->isAjaxRequest();
        }
        return $result;
    }

    /**
     * ページファイルのインクルード
     *
     * @param string $pageFile ページファイル
     *
     * @return void
     */
    public static function includePage($pageFile){
        self::$_isRunnable = false;
        include_once _BEAR_APP_HOME . '/htdocs/' . $pageFile;
        self::$_isRunnable = true;
    }
}
