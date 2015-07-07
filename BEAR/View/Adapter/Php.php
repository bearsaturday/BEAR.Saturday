<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_View
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$ $
 * @link       http://www.bear-project.net/
 */

/**
 * PHPビューアダプター
 *
 * @category   BEAR
 * @package    BEAR_View
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$ Page.php 1076 2009-10-20 00:39:19Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net/
 *
 * @Singleton
 *
 * @config   string ua           UAコード
 * @config   array  values       アサインした値
 * @config   array  agent_config UAスニッフィング用設定
 * @optional string agent_config UA設定
 */
class BEAR_View_Adapter_Php extends BEAR_View_Adapter implements BEAR_View_Interface
{
    /**
     * @var Smarty
     */
    protected $_smarty;

    /**
     * ページバリュー
     *
     * @var array
     */
    private $_values = array();

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
    }

    /**
     * UAスニッフィングインジェクト
     *
     * UAスニッフィングOnの時のインジェクタです。BEAR_Viewで指定していされています。
     *
     * @return void
     */
    public function onInjectUaSniffing()
    {
    }

    /**
     * ビューに値をセット
     *
     * @param array $values ビューにセットする値
     *
     * @return void
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
    public function display($tplName = null, array $options = array())
    {
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
        if (!file_exists($file)) {
            //テンプレートファイルがない
            $info = array(
                'tpl name' => $tplName,
                'template_file' => $file,
                'set' => $this->_values
            );
            $msg = 'Template file is missing.（テンプレートファイルがありません)';
            throw $this->_exception($msg, array('info' => $info));
        }
        // ページバリューアサイン
        $this->_smarty->assign($this->_values);
        $html = $this->_smarty->fetch($file);
        return $html;
    }
}
