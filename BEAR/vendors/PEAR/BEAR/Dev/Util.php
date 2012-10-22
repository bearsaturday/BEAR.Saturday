<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Dev
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Shell.php 1083 2009-10-21 18:16:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Dev/BEAR_Dev_Util.html
 */
/**
 * BEAR Devユーティリティクラス
 *
 * <pre>
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Dev
 * @subpackage Shell
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Shell.php 1083 2009-10-21 18:16:01Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Dev/BEAR_Dev_Util.html
 */
 class BEAR_Dev_Util
 {
    /**
     * BEARバッジ表示
     *
     * <pre>
     * エラー状態を表し、__bearページにリンクするデバック時に
     * 画面右上に現れる「BEARバッジ」を表示します。
     *
     * ページの状態によって色が変わります。
     *
     * 赤　Fatal, PEARエラーなど
     * 黄　Warningレベルのエラーはあり
     * 青　noticeは出てる
     * 緑 noticeも出てない
     * </pre>
     *
     * @static
     */
    public static function onOutpuHtmlDebug($html)
    {
        $ua = BEAR::dependency('BEAR_Agent')->getUa();
        if ($ua !== BEAR_Agent::UA_DEFAULT){
            return $html;
        }
        $app = BEAR::get('app');
        if (!$app['core']['debug']) {
            return;
        }
        // エラー統計
        $errorFgColor = "white";
        $errorStat = Panda::getErrorStat();
        if ($errorStat & E_ERROR) {
            $errorBgColor = "red";
            $errorMsg = "Fatal Error";
        } elseif ($errorStat & E_WARNING) {
            $errorBgColor = "yellow";
            $errorFgColor = "black";
            $errorMsg = "Warning";
        } elseif ($errorStat & E_NOTICE) {
            $errorBgColor = "#2D41D7";
            $errorMsg = "Notice";
        } else {
            $errorBgColor = "green";
            $errorMsg = '';
        }
        // デバック情報表示HTML
        // bear.jsを使用する場合はbear_debuggingがtrueになる
        if (file_exists(_BEAR_APP_HOME. '/htdocs/__edit')){
            $editHtml = '<a href="/__edit/" target="bearedit" name="';
            $editHtml .= $errorMsg . '" style="padding:5px 3px 3px 3px;background-color: gray';
            $editHtml .= ';color:' . $errorFgColor . ';font:bold 8pt Verdana; margin-top:100px; ';
            $editHtml .= 'border: 1px solid #dddddd">EDIT</a>';
        } else {
        	$editHtml = '';
        }
        $budgeHtml = '<div id="bear_budge" style=" font-size: 9px; position: absolute;  top: 0px;';
        $budgeHtml .= ' right: 0px; text-align: right;">';
        $budgeHtml .= $editHtml;
        $budgeHtml .= '<a href="/__bear/" target="bearlog" name="';
        $budgeHtml .= $errorMsg . '" style="padding:5px 3px 3px 3px;background-color:' . $errorBgColor;
        $budgeHtml .= ';color:' . $errorFgColor . ';font:bold 8pt Verdana; margin-top:100px; ';
        $budgeHtml .= 'border: 1px solid #dddddd;　">BEAR</a></div>' . '</body>';
        $budgeHtml = str_replace('</body>', $budgeHtml, $html);
        return $budgeHtml;
    }
 }