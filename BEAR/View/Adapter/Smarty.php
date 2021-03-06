<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Smartyビューアダプター
 *
 * @Singleton
 *
 * @config   string ua           UAコード
 * @config   array  values       アサインした値
 * @config   array  agent_config UAスニッフィング用設定
 * @optional string agent_config UA設定
 */
class BEAR_View_Adapter_Smarty extends BEAR_View_Adapter implements BEAR_View_Interface
{
    /**
     * @var Smarty
     */
    protected $_smarty;

    /**
     * @var BEAR_Log
     */
    protected $_log;

    /**
     * ページバリュー
     *
     * @var array
     */
    protected $_values = [];

    /**
     * エージェントロール
     *
     * @var array
     */
    private $_role = [];

    /**
     * JS有効スイッチ
     *
     * @var bool
     */
    private $_enableJs = true;

    /**
     * Inject
     */
    public function onInject()
    {
        if (! isset($this->_config['ua'])) {
            $this->_config['ua'] = '';
        }
        $smartyConfig = ['ua' => $this->_config['ua']];
        $this->_smarty = BEAR::dependency('BEAR_Smarty', $smartyConfig);
        $this->set($this->_config['values']);
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * UAスニッフィングインジェクト
     *
     * UAスニッフィングOnの時のインジェクタです。BEAR_Viewで指定していされています。
     */
    public function onInjectUaSniffing()
    {
        $this->onInject();
        $this->_config['values']['agent'] = $this->_config['agent_config'];
        $this->_enableJs = $this->_config['agent_config']['enable_js'];
        $this->_role = $this->_config['agent_config']['role'];
        $this->_emoji = BEAR::dependency('BEAR_Emoji');
    }

    /**
     * ビューに値をセット
     *
     * @param array $values ビューにセットする値
     */
    public function set(array $values)
    {
        $this->_values = $values;
    }

    /**
     * 表示
     *
     * ビューにセットされたバリューをテンプレートに適用して画面表示するHTTPボディとHTTPヘッダーを返します
     *
     * @param string $tplName テンプレート名
     * @param array  $options オプション
     *
     * @return BEAR_Ro
     */
    public function display($tplName = null, array $options = [])
    {
        // Pageバリューアサイン
        $this->_smarty->assign($this->_values);
        // フォームアサイン
        $forms = BEAR_Form::renderForms($this->_smarty, $this->_config['ua'], $this->_enableJs);
        $this->_smarty->assign($forms);
        // テンプレート
        $viewInfo = $this->_getViewInfo($tplName, $this->_role, 'tpl');
        $this->_smarty->assign('layout', $viewInfo['layout_value']);
        if (isset($options['layout'])) {
            $layoutfile = 'layouts/' . $options['layout'];
        } elseif (isset($viewInfo['layout_file'])) {
            $layoutfile = $viewInfo['layout_file'];
        } else {
            $layoutfile = null;
        }
        if (isset($layoutfile)) {
            $this->_smarty->assign('content_for_layout', $this->fetch($viewInfo['page_template']));
            $finalPath = $layoutfile;
        } else {
            // レイアウトなしのそのままフェッチ
            $finalPath = $viewInfo['page_template'];
        }
        $html = $this->fetch($finalPath);
        $ro = $this->_getRo($html);
        // 使用テンプレートのログ
        $this->_log->log('view', $viewInfo);

        return $ro;
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
        if (substr($tplName, 0, 1) == '/') {
            $file = $tplName;
        } else {
            $file = _BEAR_APP_HOME . $this->_config['path'] . $tplName;
        }
        if (! file_exists($file)) {
            //テンプレートファイルがない
            $info = [
                'tpl name' => $tplName,
                'template_file' => $file,
                'set' => $this->_values
            ];
            $msg = 'Template file is missing.（テンプレートファイルがありません)';

            throw $this->_exception($msg, ['info' => $info]);
        }
        // ページバリューアサイン
        $this->_smarty->assign($this->_values);

        return $this->_smarty->fetch($file);
    }
}
