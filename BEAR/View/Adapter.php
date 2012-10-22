<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Query.php 1021 2009-10-13 04:04:08Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * ビューアダプター
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Query.php 1021 2009-10-13 04:04:08Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */
abstract class BEAR_View_Adapter extends BEAR_Base
{
    /**
     * @var BEAR_Emoji
     */
    protected $_emoji;

    /**
     * テンプレート名の取得
     *
     * @param string $tplName テンプレート名（省略可）
     * @param array  $role    エージェントロール
     * @param $ext   $ext     拡張子
     *
     * @return array
     */
    protected function _getViewInfo($tplName = null, array $role = array(), $ext = 'tpl')
    {
        $result = array();
        $pagePath = $this->_getTemplateNameByPageClass($tplName); // ex) user/create
        // エージェントロール対応ページテンプレート
        if($role && (!(count($role) === 1 && $role[0] === BEAR_Agent::UA_DEFAULT))) {
            foreach ($role as $agent) {
                $agentExtention = '.' . strtolower($agent);
                $pagePathFull = _BEAR_APP_HOME . $this->_config['path']. "pages/{$pagePath}{$agentExtention}.tpl";
                if (file_exists($pagePathFull)) {
                    break;
                }
                $agentExtention = '';
            }
        } else {
            $agentExtention = '';
        }
        $matches = array();
        preg_match('/(.+?)[\.]/', $pagePath, $matches);
        if (is_array($matches)) {
            // $firstWordForConfig = index.
            $firstWordForConfig = (isset($matches[0]) && $matches[0]) ? $matches[0] : $pagePath . '.';
        } else {
            $firstWordForConfig = '';
        }
        if (substr($pagePath, 0, 1) == '/') {
            $templatePath = "{$pagePath}{$agentExtention}.{$ext}";
        } else {
            $templatePath = "pages/{$pagePath}{$agentExtention}.{$ext}";
        }
        $configFileHead = _BEAR_APP_HOME . $this->_config['path']. 'pages/' . $firstWordForConfig;
        // 設定ファイル
        if (file_exists($configFileHead . 'yml')) {
            $configFilePath = $configFileHead . 'yml';
        } elseif (file_exists($configFileHead . 'ini')) {
            $configFilePath = $configFileHead . 'ini';
        } else {
            $configFilePath = false;
        }
        $yml = $configFilePath ? BEAR::loadValues($configFilePath) : array();
        $layoutValue = ($yml !== array()) ? $this->_getRoleLayoutValue($role, $yml) : array();
        $result['page_template'] = $templatePath;
        $result['layout_value'] = $layoutValue;
        if (isset($yml['layout'])) {
            $layoutFile = $this->_getRoleFile(
                $role, _BEAR_APP_HOME . $this->_config['path'] . 'layouts/',
                $yml['layout']
            );
            $result['layout_file'] = 'layouts/' . $layoutFile;
        }
        return $result;
    }


    /**
     * リソースオブジェクトに変換
     *
     * <pre>
     * $htmlを受け取りエージェント依存のヘッダーを付加してリソースオブジェクトにして返します。
     * 返されたリソースオブジェクトはoutputHttp()メソッドでHTTP出力ができます。
     * </pre>
     *
     * @param string $html HTML 文字列
     *
     * @return BEAR_ro
     */
    protected function _getRo($html)
    {
        // ヘッダーを出力
        $header = isset($this->_config['agent_config']['header']) ? $this->_config['agent_config']['header'] : array();
        // 絵文字＆（&文字コード）フィルター
        if (isset($this->_config['agent_config']['agent_filter'])
            && $this->_config['agent_config']['agent_filter'] === true
        ) {
            $html = $this->_agentFilter($html);
        }
        // ボディ出力
        $ro = BEAR::factory('BEAR_Ro');
        /** @var $ro BEAR_Ro */
        $ro = $ro->setHeaders(array($header))->setBody($html);
        return $ro;
    }

    /**
     * 絵文字用アオウトプットフィルター
     *
     * <pre>
     * 絵文字を画像表示します。ネイティブ表示できる場合はそちらを優先します。
     * </pre>
     *
     * @param string $html HTML
     *
     * @return string
     */
    protected function _agentFilter($html)
    {
        $agentConfig = $this->_config['agent_config'];
        if (isset($agentConfig['output_encode'])) {
            $html = mb_convert_encoding($html, $agentConfig['output_encode'], 'utf-8');
        }
        // SBの場合のvalidation=""の中に入った文字のアンエスケープ
        if (isset($this->_config['ua']) && $this->_config['ua'] == BEAR_Agent::UA_SOFTBANK) {
            $html = $this->_emoji->unescapeSbEmoji($html);
        }
        // QFによりエスケープされてしまった絵文字エンティティをアンエスケープ
        // (フィルターによりバイナリにパックされる）
        // エンティティ絵文字変換 &#ddddd;
        $html = preg_replace('/&amp;#(\d{5});/s', "&#$1;", $html);
        /** @noinspection PhpUndefinedMethodInspection */
        $html = BEAR::dependency('BEAR_Emoji')->convertEmojiImage($html);
        // 絵文字バイナリ化
        if (isset($this->_config['ua']) && $this->_config['ua'] !== BEAR_Agent::UA_SOFTBANK) {
            $html = preg_replace_callback('/&#(\d{5});/s', array(__CLASS__, 'onPackEmoji'), $html);
        }
        // remove CSS
        if ($agentConfig['enable_css'] === false) {
            $html = preg_replace('!<style.*?>.*?</style.*?>!is', '', $html);
        }
        //Docomo CSS
        if ($agentConfig['enable_inline_css'] === true) {
            include _BEAR_BEAR_HOME . '/BEAR/vendors/toInlineCSSDoCoMo/toInlineCSSDoCoMo.php';
            try {
                $html = toInlineCSSDoCoMo::getInstance()->setBaseDir(_BEAR_APP_HOME . '/htdocs')->apply($html);
            } /** @noinspection PhpUndefinedClassInspection */ catch (Expection $e){
                //FB::warn($e);
            }
        }
        // remove JS
        if ($agentConfig['enable_js'] === false) {
            $html = preg_replace('!<script.*?>.*?</script.*?>!is', '', $html);
        }
        return $html;
    }

    /**
     * レイアウトバリューの取得
     *
     * レイアウトymlファイルに記述されたスタティック変数がロール通りにあれば使用します。
     *
     * @param array $role ロール
     * @param array $yml  ymlファイルの値
     *
     * @return array
     */
    private function _getRoleLayoutValue(array $role, array $yml)
    {
        $layoutValue = isset($yml['default']) ? $yml['default'] : array();
        $role = array_reverse($role);
        foreach ($role as $agent) {
            $agent = strtolower($agent);
            if (isset($yml[$agent])) {
                $layoutValue = array_merge($layoutValue, $yml[$agent]);
            }
        }
        return $layoutValue;
    }
    /**
     * エージェントロールに対応したファイルを取得
     *
     * <pre>
     * 配列でロールに応じたファイルを返します
     *
     * ex)
     * roleが'Docomo'の場合
     *
     * index.docomo.html
     * index.mobile.html
     * index.html
     *
     * というファイルに順にスキャンしてあればそれが使われます。
     *
     * @param array  $role     エージェントロール
     * @param string $dir      ディレクトリパス
     * @param string $fileName ファイル名ベース
     * @param string $ext      ファイル名拡張子
     *
     * @return string
     */
    protected function _getRoleFile($role, $dir, $fileName, $ext = 'tpl')
    {
        if (!$role) {
            return $fileName;
        }
        foreach ($role as $agent) {
            $agentExtention = '.' . strtolower($agent);
            $agentFile = str_replace(".{$ext}", "$agentExtention.{$ext}", $fileName);
            $fullPath = "$dir/{$agentFile}";
            if (file_exists($fullPath)) {
                break;
            }
            $agentFile = $fileName;
        }
        return $agentFile;
    }

    /**
     * ページクラスからパスを取得する
     *
     * <pre>
     * /はじまりだと絶対パス、テンプレート名省略または相対パスなら
     * ページクラスからパス名を組み立てます。相対パスでテンプレートを
     * 指定していれば指定したものにおきかわる。
     *
     * 例
     * 絶対パス
     *  '/index.tpl'　=> '/index'
     *  '/some/index.tpl' => '/some/index'
     * 相対パス　Sample_Test_Pageクラスの場合
     *  '' =>　'sample/test/page'
     *  'help.tpl' => 'sample/test/help'
     *  'another/help.tpl' => 'sample/test/another/help'
     * </pre>
     *
     * @param string $tplName テンプレート名
     *
     * @return string
     */
    private function _getTemplateNameByPageClass($tplName)
    {
        // 絶対パス
        if (substr($tplName, 0, 1) == '/') {
            // 先頭の/を除いて返す
            $absPath = $this->_removeExtention($tplName);
            $result = substr($absPath, 0);
            return $result;
        }
        // 相対パスの場合はクラス名からベースパスを作成する
        $pageClass = preg_replace('/^page_/i', '', $this->_config['resource_id']);
        $base = str_replace('_', '/', strtolower($pageClass));
        //テンプレート名の指定が無ければベースパスと同じ名前とみなす
        if ($tplName == null) {
            return $base;
        }
        //テンプレート名の指定があればページクラス名の相対パスで置換
        $relPath = substr($base, 0, strrpos($base, '/'));
        if ($relPath) {
            $relPath .= '/';
        }
        return $relPath . $this->_removeExtention($tplName);
    }

    /**
     * ファイルの拡張子を除いたものを返します
     *
     * @param string $file ファイル名
     *
     * @return string
     */
    private function _removeExtention($file)
    {
        $pathinfo = pathinfo($file);
        switch ($pathinfo['dirname']) {
        case '/' :
            $result = $pathinfo['filename'];
            break;
        case '.' :
            $result = $pathinfo['filename'];
            break;
        default :
            $begin = (substr($pathinfo['dirname'], 0, 1) === '/') ? 1 : 0;
            $result = substr($pathinfo['dirname'], $begin) . '/' . $pathinfo['filename'];
            break;
        }
        return $result;
    }

    /**
     * コールバック用絵文字出力
     *
     * @param array $match マッチ
     *
     * @return string
     */
    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function onPackEmoji($match)
    {
        $result = pack('n', $match[1]);
        return $result;
    }
}