<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Main
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Main.php 2564 2011-06-19 16:11:55Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * メイン
 *
 * <pre>
 * ページに実装されたイベントハンドラをイベント毎にコールしページを実行します。
 * キャッシュオプションでページの初期化（onInit）をキャッシュするinitキャッシュ、
 * テンプレート生成までも含めたページキャッシュのキャッシュオプションを
 * 指定することができます。
 *
 * Example 1.キャッシュページの実行
 * </pre>
 * <code>
 * class Page_Blog_RSS extends App_Page{
 * }
 * $config = array('page_cache'=>'init', 'life'=>60);
 * new BEAR_Main('Page_Blog_RSS', $config);
 * //10分間のページキャッシュ
 * </code>
 *
 * @category  BEAR
 * @package   BEAR_Main
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Main.php 2564 2011-06-19 16:11:55Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 *
 * @Singleton
 *
 * @config string ua                 UAコード
 * @config bool   enable_ua_sniffing UAスニッフィング
 * @config string injector           インジェクタ
 * @config bool   enable_onshutdown  onShutdown可能に
 *
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
     * @var BEAR_Page
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
    private $_isAjaxSubmit = false;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        // ページを生成してレジストリにセット
        $config = array('resource_id' => $this->_config['page_class'], 'enable_ua_sniffing' => $this->_config['enable_ua_sniffing'], 'ua' => $this->_config['ua'], 'mode' => BEAR_Page::CONFIG_MODE_HTML);
        $options = array('injector' => (isset($this->_config['injector']) ? $this->_config['injector'] : 'onInject'));
        $this->_page = BEAR::factory($this->_config['page_class'], $config, $options);
        BEAR::set('page', $this->_page);
        $this->_log = BEAR::dependency('BEAR_Log');
        $this->_roPrototype = BEAR::dependency('BEAR_Ro_Prototype');
    }

    /**
     * ページクラス実行
     *
     * 指定されたページクラスをインスタンス化し実行します。
     *
     * @param string $pageClass ページクラス名
     * @param array  $config    設定
     * @param array  $options   オプション
     *
     * @return void
     * @throws BEAR_Page_Exception
     */
    public static function run($pageClass, array $config = array(), array $options = array())
    {
        // include
        if (self::$_isRunnable === false) {
            BEAR::dependency('BEAR_Log')->log("Page Include", $pageClass);
            return;
        }
        self::$_isRunnable = false;
        // ページクラス存在チェック
        if (!$pageClass || !class_exists($pageClass, false)) {
            $info = array('page_class' => $pageClass);
            throw new BEAR_Page_Exception(
                'Page class is not defined.（ページクラスが定義されていません)',
                array('info' => $info)
            );
        }
        // フォーム初期化
        if (class_exists('BEAR_Form', false)) {
            BEAR_Form::init();
        }
        // メイン実行
        $config['page_class'] = $pageClass;
        try {
            $main = BEAR::dependency('App_Main', $config, $options);
            $main->_run($pageClass);
        } catch (BEAR_Exception $e) {
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
    protected function _run($pageClass)
    {
        if ($this->_config['enable_onshutdown'] === true) {
            register_shutdown_function(array($pageClass, 'onShutdown'));
        }
        $this->_sessionStart();
        // debugログ
        $this->_log->log('Page', $this->_config);
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
            $this->_page->setPrototypeRo();
        } else {
            $this->_page->set($initCache);
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
    protected function _runSubmit()
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
            $this->_log->log('form', array('valid' => true, 'errors' => array()));
        } else {
            // submit NG
            $this->_log->log(
            	'form',
                array('valid' => false,
                	  'rules' => $form->_rules,
                	  'errors' => $form->_errors)
            );
            if ($this->_isAjaxSubmit) {
                // AJAXバリデーションNG
                $this->_ajaxValidationNG($form);
            } else {
                $this->_runPreOnOutput();
                $this->_page->onOutput();
            }
        }
    }

    /**
     * onClickコール
     *
     * ページのonClickメソッドをコールします。
     *
     * @param array $args 引数
     *
     * @return void
     */
    protected function _runClick(array $args)
    {
        // onClick
        $isActiveLink = isset($_GET[BEAR_Page::KEY_CLICK_NAME]);
        $hasMethod = isset($_GET[BEAR_Page::KEY_CLICK_NAME])
        && method_exists($this->_page, $onClickMethod = 'onClick' . $_GET[BEAR_Page::KEY_CLICK_NAME]);
        if ($isActiveLink && $hasMethod) {
            $this->_page->setOnClick($_GET[BEAR_Page::KEY_CLICK_NAME]);
            $args['click'] = isset($_GET[BEAR_Page::KEY_CLICK_VALUE]) ? $_GET[BEAR_Page::KEY_CLICK_VALUE] : null;
            $this->_log->log('onClick', array('click' => $this->_page->getOnClick(), 'args' => $args));
            $this->_page->$onClickMethod($args);
        } else if (method_exists($this->_page, 'onClickNone')) {
            $this->_page->onClickNone($args);
        }
    }

    /**
     * onInitコール
     *
     * @param array $args ページ引数
     *
     * @return void
     * @throws Panda_Exception
     */
    protected function _runInit(array $args)
    {
        $config['class'] = get_class($this->_page);
        $config['method'] = 'onInit';
        $annotation = BEAR::factory('BEAR_Annotation', $config);
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
    protected function _runPreOnOutput()
    {
        $buff = ob_get_clean();
        ob_start();
        if ($this->_config['debug'] && $buff) {
            $ajax = BEAR::dependency('BEAR_Page_Ajax');
            if (!$ajax->isAjaxRequest()) {
                echo '<div style="border-style: dotted;">' . $buff . '</div>';
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
        if ($this->_config['enable_ua_sniffing'] === true) {
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
     * ヘッダーとコンテンツを出力して終了します。
     *
     * @return void
     * @throws BEAR_Main_Exception
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

        if ($this->_config['debug'] === true) {
            $body = BEAR_Dev_Util::onOutpuHtmlDebug($body);
            // cancel with ob_clean(), then rewrite debug budge.
            ob_clean();
            echo $body;
        }
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
    public function isTokenValid($token)
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
     * @param object $form
     * @param object $formName
     *
     * @return void
     */
    private function _formValidationOk($form, $formName = null)
    {
        $submit = $form->exportValues();
        BEAR_Form::$submitValue = $form->getSubmitValues();
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
     * exit()の実行をここに集約しています。
     *
     * @return void
     */
    public function exitMain()
    {
        unset($this->_page);
        if ($this->_config['exit_on_end']) {
            exit();
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
     * @throws BEAR_Main_Exception
     */
    public static function includePage($pageFile)
    {
        self::$_isRunnable = false;
        $fullPathPageFile = _BEAR_APP_HOME . '/htdocs/' . $pageFile;
        if (!file_exists($fullPathPageFile)) {
            throw new BEAR_Main_Exception('Page file is not exit', array('info' => array('file' => $fullPathPageFile)));
        }
        include_once $fullPathPageFile;
        self::$_isRunnable = true;
    }
}
