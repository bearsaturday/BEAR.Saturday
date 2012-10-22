<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Form
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Form.php 1311 2010-01-04 08:04:23Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Form/BEAR_Form.html
 */
/**
 * BEARフォームクラス
 *
 * <pre>
 * フォームを取り扱うクラスです。
 * PEAR::HTML_QuickFormを継承して、日本語化などの初期設定や機能を付加しています。
 * オブジェクトはシングルトンで取得します。
 * <／pre>
 *
 * @category BEAR
 * @package  BEAR_Form
 * @author   Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @license  http://opensource.org/licenses/bsd-license.php BSD
 * @version  SVN: Release: $Id: Form.php 1311 2010-01-04 08:04:23Z koriyama@users.sourceforge.jp $
 * @link     http://api.bear-project.net/BEAR_Form/BEAR_Form.html
 *  * @see      PEAR::HTML_QuickForm
 */
class BEAR_Form extends BEAR_Factory
{
    /**
     * DHTML_TABLELESSを継承したPC/Mobile対応のAppレンダラー
     *
     * @see App/Form/Renderer/Default.php
     * @see App/Form/Renderer/DefaultMobile.php
     */
    const RENDERER_APP = 0;

    /**
     * '静的な' Smarty テンプレートのためのレンダラ
     *
     * @see http://pear.php.net/manual/ja/package.html.html-quickform.html-quickform-renderer-arraysmarty.php
     */
    const RENDERER_SMARTY_ARRAY = 1;

    /**
     * 完全に妥当な XHTML を出力するレンダラ
     *
     * @see http://pear.php.net/manual/ja/package.html.html-quickform-renderer-tableless.intro.php
     *
     */
    const RENDERER_DHTML_TABLELESS = 2;


    /**
     * JS Alertのメッセージ
     *
     */
    const JS_WARNING = '入力内容に誤りがあります';

    /**
     * 必須項目メッセージ
     *
     */
    const REQUIRE_NOTES = '<span style="font-size:81%; color:#ff0000;">*</span><span style="font-size:80%;">の項目は必ず入力してください。</span>';

    /**
     * エラーテンプレート
     *
     */
    const TEMPLATE_ERROR = '{if $error}<span style="color:#ff0000;">{$label}</span>{/if}';

    /**
     * 必須項目テンプレート
     *
     */
    const TEMPLATE_REQUIRED = '{$html}{if $required}<span style="font-size:80%; color:#ff0000;">*</span>{/if}';

    /**
     * フォームエラーSmarty変数アサイン名
     *
     */
    const FORM_ERRORS = 'form_errors';

    /**
     * SubmitValue値
     *
     * <pre>
     * Quick_Form::getSubmitValues()の値、onAction($submit)の$submitは
     * Quick_Form::exportValue()で出力された値でsubmitボタンの値などは出力されない
     * </pre>
     */
    public static $submitValue;

    /**
     * サブミットヘッダー
     *
     * $submitの属性情報
     *
     * @var array
     */
    public static $submitHeader = array();

    /**
     * AJAXフォーム用フラグ
     *
     * @var bool
     * @access private
     */
    private $_isAjaxForm = false;

    /**
     * エラーテンプレート
     *
     * @var string
     */
    public static $errorTemplate = self::TEMPLATE_ERROR;

    /**
     * 必須項目テンプレート
     *
     * @var string
     */
    public static $requireTemplate = self::TEMPLATE_REQUIRED;

    /**
     * 必須項目説明表示
     *
     * @var string
     */
    public static $requireNotes = self::REQUIRE_NOTES;

    /**
     * JS警告
     */
    public static $jsWarning = self::JS_WARNING;

    /**
     * フォームレンダラ
     *
     * @var string
     */
    private static $_renderer = self::RENDERER_SMARTY_ARRAY;

    /**
     * フォーム名
     */
    public static $formNames = array();

    /**
     * 送信方法
     *
     * @var string
     */
    public static $method = 'post';

    /**
     * 使用済みトークン
     */
    private static $_usedToken = array();

    /**
     * シングルトンインスタンス
     */
    private static $_instance = null;

    /**
     * レンダラーコールバック
     *
     * @var callable
     */
    private static $_rendererCallback = null;


    private static $_renderConfig = array();

    /**
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * ファクトリー
     *
     * <pre>
     * Quick_Formオブエクトを生成して設定します。
     * </pre>
     *
     * @param array $config 設定
     *
     * @return BEAR_Form
     */
    public function factory()
    {
        $this->_config['action'] = (!isset($this->_config['action']) || $this->_config['action'] == '') ? $_SERVER['REQUEST_URI'] : $this->_config['action'];
        /** @todo FWの初期値はBEAR.ymlに集約 */
        $options = array('formName' => 'form',
            'method' => 'post',
            'action' => '',
            'target' => '',
            'attributes' => null,
            'callback' => false);
        if ($this->_config) {
            $options = array_merge($options, $this->_config);
        }
        $page = BEAR::get('page');
        $onClick = $page->getOnClick();
        if ($onClick && !($options['action'])) {
            $options['action'] = '?' . BEAR_Page::KEY_CLICK_NAME . '=' . $onClick;
        }
        //フォーム名の登録
        self::$formNames[] = $formName = $options['formName'];
        // $formObject生成
        $options['trackSubmit'] = true;
        $formObject = $this->_factory($formName, $options);
        BEAR::set('BEAR_Form_' . $formName, $formObject);
        self::$method = $options['method'];
        // set render form
        $page = BEAR::get('page');
        $page->setConfigVal('redner_form', true);
        self::$_renderConfig[$formName] =  $this->_config;
        // extra elements
        $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']['bcheckbox'] = array(_BEAR_BEAR_HOME . '/BEAR/Form/elements/bcheckbox.php', 'HTML_QuickForm_bcheckbox');
        $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']['bradio'] = array(_BEAR_BEAR_HOME . '/BEAR/Form/elements/bradio.php', 'HTML_QuickForm_bradio');
        return $formObject;
    }

    /**
     * ファクトリー
     *
     * HTML_QuickFormインスタンス生成
     *
     * @param mixed $formName フォーム名 | フォーム名配列
     * @param array $options  オプション
     *
     * @return HTML_QuickForm
     * @access private
     */
    private function _factory($formName, $options)
    {
        // QuickForm作成
        $form = new HTML_QuickForm($formName, $options['method'], $options['action'], $options['target'], $options['attributes'], $options['trackSubmit']);
        // 必須項目メッセージ日本語化
        $form->setRequiredNote(BEAR_Form::$requireNotes);
        // JSメッセージ日本語化
        $form->setJsWarnings(BEAR_Form::$jsWarning, '');
        // BEAR使用hidden項目
        $newToken = self::_saveToken($form);
        /** @todo トークン消去用シャットダウン処理登録 */
        //        PEAR::registerShutdownFunc(array('BEAR_Form', 'onShutDown'));
        $log = $options;
        $log['formNames'] = $formName;
        $log['token'] = $newToken;
        $this->_log->log('Form', $log);
        return $form;
    }

    /**
     * インスタンス解放
     *
     * ページの再生成で使用されます
     *
     * @return void
     */
    public static function init()
    {
        self::$_instance = null;
    }

    /**
     * フォームレンダラ指定
     *
     * フォームのレンダラーを指定します。
     * <pre>
     * BEAR_FORM::setRenderer(BEAR_FORM::RENDERER_SMARTY_ARRAY); //デファオルト
     * BEAR_FORM::setRenderer(BEAR_FORM::RENDERER_DHTML_TABLELESS); //DHTML
     *
     * @param string $renderer フォームレンダラー
     *
     * @return void
     *
     * @see http://pear.php.net/manual/ja/package.html.html-quickform-renderer-tableless.intro.php
     */
    public static function setRenderer($renderer)
    {
        self::$_renderer = $renderer;
    }

    /**
     * 現在のレンダラーを返します
     *
     * @return string
     */
    public static function getRenderer()
    {
        return self::$_renderer;
    }

    /**
     * トークンの保存
     *
     * 二重送信防止とCSSF防止のため、セッションとhiddenに埋め込みます
     *
     * @param object &$form フォームオブジェクト
     *
     * @return string $newToken
     */
    private static function _saveToken(&$form)
    {
        $newToken = self::makeToken();
        $form->addElement('hidden', '_token', $newToken);
        return $newToken;
    }

    /**
     * トークン生成
     *
     * <pre>14桁の16進数トークンを生成。
     * 前12桁がデータ、残り2桁がチェックサム。
     * スタティックコールできます。
     *
     * データ例）
     * 8486ab282a8f37
     * <--data----><check sum>
     * </pre>
     *
     * @param bool $isAjax AJAXかどうか
     *
     * @return string
     * @static
     */
    public static function makeToken($isAjax = false)
    {
         $session = BEAR::dependency('BEAR_Session');
        if ($isAjax) {
            $tokenBody = 'a' . $session->get(BEAR_Session::SESSION_TOKEN) . substr(md5(uniqid(rand(), true)), 0, 8);
        } else {
            $tokenBody = '0' . $session->get(BEAR_Session::SESSION_TOKEN) . substr(md5(uniqid(rand(), true)), 0, 8);
        }
        $result = $tokenBody . substr(md5(session_id()), 0, 4);
        return $result;
    }

    /**
     * セッショントークンの取得
     *
     * セッション開始時につくられるトークンを取得します
     *
     * @return string
     * @static
     */
    public static function getSessionToken()
    {
        $session = BEAR::dependency('BEAR_Session');
        $result = $session->get(BEAR_Session::SESSION_TOKEN);
        return $result;
    }

    /**
     * フォームをAJAX用にする
     *
     * <pre>QuickFormのフォームをAJAX対応にします。formタグにrel=bearという属性が追加され
     * postがAJAXリクエストに変わります</pre>
     *
     * Example.1 ページ内でフォームをAJAXフォームに変換
     *
     * <code>
     * $this->form->ajax();
     * </code>
     *
     * @return void
     * @ignore
     *
     */
    public function ajax()
    {
        $tokenRef = & $this->getElement('_token');
        if (!PEAR::isError($tokenRef)) {
            $tokenRef->_attributes['value'] = App_Main::makeToken(true);
        } else {
            $this->_log->log('BEAR_Error', array(
                'Erro no token for AJAX'));
        }
        $attr = $this->_attributes;
        $attr['rel'] = "bear";
        $this->setAttributes($attr);
        $this->_isAjaxForm = true;
    }

    /**
     * AJAXフォームかどうかを返す
     *
     * @return bool
     * @ignore
     */
    public function isAjaxForm()
    {
        return $this->_isAjaxForm;
    }

    /**
     * サブミットヘッダーを追加
     *
     * サブミットの装飾値をヘッダーとして付加します。
     *
     * これは例えばフォームの遷移の状態などフォームそのものではないが
     * フォームとして利用したい時に使用します。
     *
     * onAction($submit)の$submitには渡りません。
     *
     * @param HTML_Quick_Form $form  QuickForm
     * @param stirng          $key   ヘッダーキー
     * @param string          $value ヘッダーの値
     *
     * @return void
     */
    public static function setSubmitHeader($form, $key, $value)
    {
        $form->addElement('hidden', '_' . $key, $value);
    }

    /**
     * サブミットヘッダーの取得
     *
     * @param string $submitHeaderKey ヘッダーのキー
     *
     * @return string
     *
     * @see BEAR_Form::setSubmitHeader
     */
    public static function getSubmitHeader($submitHeaderKey = null)
    {
        $post = array();
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                $post[substr($key, 1)] = $value;
            }
        }
        if (!isset($post[$submitHeaderKey])) {
            $result = $post;
        } else {
            $result = isset($post[$submitHeaderKey]) ? $post[$submitHeaderKey] : null;
        }
        return $result;
    }

    /**
     * Submitされたフォームの名前を取得
     *
     * マルチフォームの場合にどのフォームでサブミットされたかを調べます。
     *
     * @param array $submits サブミット値
     *
     * @return string フォーム名
     */
    public static function getSubmitFormName($submits)
    {
        foreach ($submits as $postName => $postValue) {
            if (preg_match('/^_qf__(.+)/', $postName, $match) > 0) {
                $formName = $match[1];
                break;
            }
        }
        return $formName;
    }

    /**
     * フォームレンダリング
     *
     * フォームをレンダリングします
     *
     * @return void
     */
    public static function renderForms(&$smarty, $ua, $removeJs = false)
    {
        static $result = false;
        static $done = false;

        if ($done === true) {
            return $result;
        }
        $done = true;
        $result = array();
        foreach (BEAR_Form::$formNames as $formName) {
        	$renderConfig = self::$_renderConfig[$formName];
        	($renderConfig);
        	$adaptor = isset($renderConfig['adaptor']) ? $renderConfig['adaptor'] : BEAR_Form::RENDERER_APP;
            $form = BEAR::get('BEAR_Form_' . $formName);
            $formErrors = false;
            $callback = (isset($renderConfig['callback']) && is_callable($renderConfig['callback'], false)) ? $renderConfig['callback'] : false;
            switch ($adaptor) {
            case BEAR_Form::RENDERER_APP :
                // DHTMLRulesTablelessレンダラ
                //単数フォーム
                $renderer = BEAR::dependency('App_Form_Renderer_' . $ua);
            	assert(is_object($renderer));
                if ($callback) {
                	call_user_func($callback, $renderer);
                }
                $form->accept($renderer);
                // 完全なXHTML1.1に
                $form->removeAttribute('name');
                $formValue = $renderer->toHtml();
                $formErrors = $form->_errors;
                break;
            case BEAR_Form::RENDERER_DHTML_TABLELESS :
                // DHTMLRulesTablelessレンダラ
                //単数フォーム
                $renderer = new HTML_QuickForm_Renderer_Tableless($form);
                // onblur有効
                $form->getValidationScript();
                $form->accept($renderer);
                // 完全なXHTML1.1に
                $form->removeAttribute('name');
                if ($callback) {
                	call_user_func($callback, $renderer);
                }
                $formValue = $renderer->toHtml();
                $formErrors = $form->_errors;
                break;
            case BEAR_Form::RENDERER_SMARTY_ARRAY :
            default :
                // HTML_QuickForm_Renderer_ArraySmartyレンダラ
                //フォーム描画
                $page = BEAR::get('page');
                $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                $renderer->setRequiredTemplate(BEAR_Form::$requireTemplate);
                $renderer->setErrorTemplate(BEAR_Form::$errorTemplate);
                if ($callback) {
                	call_user_func($callback, $renderer);
                }
                $form->accept($renderer);
                $formValue = $renderer->toArray();
                break;
            }
            if (is_array($formErrors) && $formErrors) {
                $formErrorSummary = '<div class="form-error-summary"><ul><li>' . implode('</li><li>', $formErrors) . '</li></ul></div>';
            	$smarty->assign('formErrorSummary', $errorSummary);
            } else {
            	$formErrorSummary = '';
            }
            //remove Javascript code if Docomo or AU
            if (is_array($formValue) && isset($formValue['javascript']) && $removeJs) {
                unset($formValue['javascript']);
            } elseif (is_string($formValue) && $removeJs) {
            	// @todo unset js if string
            }
			$smarty->assign($formName, $formValue);
			$result[$formName] = $formValue;
        }
        return $result;
    }

    /**
     * フォームの数を返す
     *
     * @return int
     */
    public static function getFormNumber()
    {
        return count(BEAR_Form::$formNames);
    }

    /**
     * 使用トークンを仮登録
     *
     * <pre>
     * 登録されたトークンはリソースリクエストで例外が発生しなければ
     * 使用済みとしてマークされます。
     * </pre>
     *
     * @param string $token トークンID
     *
     * @return void
     */
    static public function registerUsedToken($token)
    {
        self::$_usedToken[] = $token;
    }

    /**
     * 登録されたトークンを使用済みとしてマーク
     *
     * @return void
     */
    public static function finishTokens()
    {
        foreach (self::$_usedToken as $token) {
            $_SESSION['_used_token'][$token] = 1;
        }
    }
}