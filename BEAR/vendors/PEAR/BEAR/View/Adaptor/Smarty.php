<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_View_Smarty
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: $
 * @link      http://api.bear-project.net/BEAR_View_Smarty/BEAR_View_Smarty.html
 */
/**
 * BEAR_View_Smartyクラス
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
 * @package   BEAR_View_Smarty
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Page.php 1076 2009-10-20 00:39:19Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_View_Smarty/BEAR_View_Smarty.html
 * @abstract
 */
class BEAR_View_Adaptor_Smarty extends BEAR_View_Adaptor implements BEAR_View_Interface
{

    /**
     * ページバリュー
     *
     * @var array
     */
    private $_values = array();

    /**
     * エージェント設定
     *
     * @var array
     */
    private $_agentConfig = array();

    /**
     * エージェントロール
     *
     * @var array
     */
    private $_role = array();

    /**
     * JS有効スイッチ
     *
     * @var bool
     */
    private $_enableJs = true;


    /**
     * コンストラクタ
     *
     * @param array $config
     *
     * @optional ua
     * @optional agent_config
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
        if (!isset($this->_config['ua'])) {
            $this->_config['ua'] = '';
        }
        $smartyConfig = array('ua' => $this->_config['ua']);
        $this->_smarty = BEAR::dependency('BEAR_Smarty', $smartyConfig);
        $this->set($this->_config['values']);
    }

    /**
     * UAスニッフィングインジェクト
     *
     */
    public function onInjectUaSniffing()
    {
        $smartyConfig = array('ua'=>$this->_config['ua']);
        $this->_smarty = BEAR::dependency('BEAR_Smarty', $smartyConfig);
        $this->_config['values']['agent'] = $this->_config['agent_config'];
        $this->set($this->_config['values']);
        $this->_enableJs =  $this->_config['agent_config']['enable_js'];
        $this->_role = $this->_config['agent_config']['role'];
        $this->_emoji = BEAR::dependency('BEAR_Emoji');
    }

    /**
     * ビューに値をセット
     *
     * @param array $values
     */
    public function set(array $values)
    {
        $this->_values = $values;
    }

    /**
     * 表示
     *
     * ビューにセットされたバリューをテンプレートに適用して画面表示します。
     *
     * @param string $tplName
     */
    public function display($tplName = null)
    {
    	// ページバリューアサイン
        $this->_smarty->assign($this->_values);
        // フォームアサイン
        $forms = BEAR_Form::renderForms($this->_smarty, $this->_config['ua'], $this->_enableJs);
        $this->_smarty->assign($forms);
        // テンプレート
        $viewInfo = $this->_getViewInfo($tplName, $this->_role, 'tpl');
        $this->_smarty->assign('layout', $viewInfo['layout_value']);
        if (isset($viewInfo['layout_file'])) {
            $this->_smarty->assign('content_for_layout', $this->fetch($viewInfo['page_template']));
            $finalPath = $viewInfo['layout_file'];
        } else {
            // レイアウトなしのそのままフェッチ
            $finalPath =$viewInfo['page_template'];
        }
        $html = $this->fetch($finalPath);
        $this->_output($html);
        // 使用テンプレートのログ
        $this->_log->log('view',$viewInfo);
    }

    /**
     * ビュー文字列取得
     *
     * アサイン済みテンプレートのHTMLを文字列として取得します。
     *
     * @param string $tplName テンプレート名
     *
     * @return string
     */
    public function fetch($tplName)
    {
        // プレフィックス付きテンプレートファイル優先
        // 無ければプレフィックス無しを使用
        $file = _BEAR_APP_HOME . '/App/views/' . $tplName;
        if (!file_exists($file)) {
            //テンプレートファイルがない
            $info = array(
                'tpl name' => $tplName,
                'template_file' => $file);
            $msg = 'Template file is missing.（テンプレートファイルがありません)';
            throw $this->_exception($msg, array('info' => $info));
        }
        // ページバリューアサイン
        $this->_smarty->assign($this->_values);
        $html = $this->_smarty->fetch($file);
        return $html;
    }


    /**
     * http出力
     *
     * <pre>
     * HTTPヘッダーとコンテンツを出力します。
     * HTMLコンテンツ(text/html)やXMLコンテンツ(application/xml)などを出力します。
     *
     * Example 1. XML出力
     * </pre>
     * <code>
     * //XML出力
     * $option = array('header' => "Content-Type: application/xml");
     * $this->_output($contens, $option);
     * </code>
     *
     * Example 2. 複数ヘッダー出力
     *
     * <code>
     * $option[] = array('header' => "Content-Type: application/xml");
     * $option[] = array('x-hoge-time' => "{$time}");
     * $this->_output($contens, $option);
     * </code>
     *
     * @param string $html    HTMLコンテンツ
     * @param mixed  $headers HTTPヘッダー
     *
     * @return mixed
     */
    private function _output($html)
    {
        // ヘッダーを出力
        if (isset($this->_config['agent_config']['header']) && $this->_config['agent_config']['header']) {
            BEAR::dependency('BEAR_Page_Header')->setHeader($this->_config['agent_config']['header']);
        }
        // 絵文字＆（&文字コード）フィルター
        if (isset($this->_config['agent_config']['agent_filter']) && $this->_config['agent_config']['agent_filter'] === true) {
            $html = $this->agentFilter($html);
        }
        // ボディ出力
        echo $html;
    }

    /**
     * 絵文字用アオウトプットフィルター
     *
     * <pre>
     * 絵文字を画像表示します。ネイティブ表示できる場合はそちらを優先します。
     * </pre>
     *
     * @param string $html   HTML
     * @param Smarty $smarty smartyオブジェクト
     *
     * @return string
     * @static
     */
    public function agentFilter($html)
    {
    	$agentConfig = $this->_config['agent_config'];
        if (isset($agentConfig['output_encode'])) {
            $html = mb_convert_encoding($html, $agentConfig['output_encode'], 'utf-8');
        }
        // SBの場合のvalidation=""の中に入った文字のアンエスケープ
	    if ($this->_config['agent_config']['ua'] == 'v'){
	        $html = $this->_emoji->unescapeSbEmoji($html);
	    }
	    // QFによりエスケープされてしまった絵文字エンティティをアンエスケープ
	    // (フィルターによりバイナリにパックされる）
        // エンティティ絵文字変換 &#ddddd;
        $regex = '/&amp;#(\d{5});/s';
        $html = preg_replace($regex, "&#$1;", $html);
        $html = $this->_emoji->convertEmojiImage($html);
        // エージェント絵文字変換 {emoji SIGN}
        $regex = '/{emoji ([A-Z]+)}/s';
        $html = preg_replace_callback($regex, array(__CLASS__, 'Emoji'), $html);
        // remove CSS
        if ($agentConfig['enable_css'] === false) {
            $html = preg_replace('!<style.*?>.*?</style.*?>!is', '', $html);
        }
        //Docomo CSS
        if ($agentConfig['enable_inline_css'] === true) {
	        include _BEAR_BEAR_HOME . '/BEAR/inc/toInlineCSSDoCoMo/toInlineCSSDoCoMo.php';
	        try {
	           $html = toInlineCSSDoCoMo::getInstance()->setBaseDir(_BEAR_APP_HOME . '/htdocs')->apply($html);
	        } catch (Expection $e){
	        	FB::warn($e);
	        }
        }
        // remove JS
        if ($agentConfig['enable_js'] === false) {
            $html = preg_replace('!<script.*?>.*?</script.*?>!is', '', $html);
        }
        return $html;
    }

    /**
     * コールバック用絵文字出力
     *
     * @param $match
     * @return string
     *
     * @ignore
     */
    public static function emoji($match)
    {
    	return BEAR::dependency('BEAR_Emoji')->getAgentEmoji($match[1]);
    }

}