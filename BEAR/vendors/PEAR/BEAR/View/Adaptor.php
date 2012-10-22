<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Query.php 1021 2009-10-13 04:04:08Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
/**
 * ビュークラス
 *
 *
 * @category  BEAR
 * @package   BEAR_View
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Query.php 1021 2009-10-13 04:04:08Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
abstract class BEAR_View_Adaptor extends BEAR_Base
{
    /**
     * テンプレート名の取得
     *
     * @param string $tplName テンプレート名（省略可）
     *
     * @return array
     *
     */
    protected function _getViewInfo($tplName = null, array $role = array(), $ext = 'tpl')
    {
        $result = array();
        $pagePath = $this->_getTemplateNameByPageClass($tplName); // ex) user/create
        // エージェントロール対応ページテンプレート
        if ($role) {
            foreach ($role as $agent) {
                $agentExtention = '.' . strtolower($agent);
                $pagePathFull = _BEAR_APP_HOME . "/App/views/pages/{$pagePath}{$agentExtention}.tpl";
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
        $templatePath = "pages/{$pagePath}{$agentExtention}.{$ext}";
        $configFileHead = _BEAR_APP_HOME . '/App/views/pages/' . $firstWordForConfig;
        //$configFileHead like bear.kumatter/App/views/pages/index.
        // 設定ファイル
        if (file_exists($configFileHead . 'yml')) {
            $configFilePath = $configFileHead . 'yml';
        } elseif (file_exists($configFileHead . 'ini')) {
            $configFilePath = $configFileHead . 'ini';
        } else {
            $configFilePath = false;
        }
        $config = $configFilePath ? BEAR::loadValues($configFilePath) : array();
        $layoutValue = isset($config['default']) ? $config['default'] : array();
        $ua = (isset($this->_config['agent_config']['ua'])) ? $this->_config['agent_config']['ua'] : '';
        $uaLow = strtolower($ua);
        $configKeys = array_keys($config);
        $idx = array_search($uaLow, $configKeys);
        if ($idx) {
            $availableAgent = $configKeys[$idx];
            $layoutValue = array_merge($layoutValue, $config[$availableAgent]);
        }
        $result['page_template'] = $templatePath;
        $result['layout_value'] = $layoutValue;
        if ($config['layout']) {
	        $layoutFile = $this->_getRoleFile($role,  _BEAR_APP_HOME . '/App/views/layouts/',  $config['layout']);
	        $result['layout_file'] = 'layouts/' . $layoutFile;
        }
        return $result;
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
     * @param array  $role
     * @param string $dir
     * @param string $fileName
     */
    protected function _getRoleFile($role, $dir, $fileName, $ext = 'tpl')
    {
        if (!$role) {
            return  $fileName;
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
            return substr($absPath, 0);
        }
        // 相対パスの場合はクラス名からベースパスを作成する
        $pageClass = preg_replace('/^Page_/', '', $this->_config['BEAR_Main']['page_class']);
        $base = str_replace('_', '/', strtolower($pageClass));
        if ($tplName == null) {
            return $base;
        }
        $bodyTpl = $this->_removeExtention($tplName);
        //テンプレート名の指定があればページクラス名の相対パスで置換
        $regx = '/\/([\w]+)$/i';
        $result = ($tplName) ? preg_replace($regx, '/' . $bodyTpl, $base) : $base;
        return $result;
    }

    /**
     * ファイルの拡張子を除いたものを返します
     *
     * @param string $file ファイル名
     *
     * @return string
     *
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
                $result = $pathinfo['dirname'] . '/' . $pathinfo['filename'];
                break;
        }
        return $result;
    }

}