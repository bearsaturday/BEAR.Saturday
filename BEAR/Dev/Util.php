<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Dev
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * BEAR Devユーティリティ
 *
 * @category  BEAR
 * @package   BEAR_Dev
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net
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
     * </pre>
     * <ur>
     * <li>赤　Fatal, PEARエラーなど</li>
     * <li>黄　Warningレベルのエラーはあり</li>
     * <li>青　noticeは出てる</li>
     * <li>緑 noticeも出てない</li>
     * </url>
     *
     * @return string
     */
    public static function onOutpuHtmlDebug($html)
    {
        $ua = BEAR::dependency('BEAR_Agent')->getUa();
        $hasResource = BEAR::factory('BEAR_Ro_Debug')->hasResourceDebug();
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
            $errorMsg = 'No Error';
        }
        // デバック情報表示HTML
        // bear.jsを使用する場合はbear_debuggingがtrueになる
        if (file_exists(_BEAR_APP_HOME. '/htdocs/__edit')) {
            $editHtml = '<a href="/__edit/?id=@@@log_id@@@"';
            $editHtml .= ' class="bear_page_edit" style="padding:5px 3px 3px 3px;background-color: gray';
            $editHtml .= ';color:white; font:bold 8pt Verdana;';
            $editHtml .= 'border: 1px solid #dddddd">EDIT</a>';
        } else {
            $editHtml = '';
        }
        // リソースBoxリンク
        $color = "blue";
        $res = array();
        if (!isset($_GET['_resource'])) {
            $mode = 'box';
            $title = "Resource Box";
            $color = "grey";
        } elseif ($_GET['_resource'] == 'box') {
            $mode = 'body';
            $title = "Resource Body";
        } elseif ($_GET['_resource'] == 'body') {
            $mode = 'html';
            $title = "Resource HTML";
        } else {
            $mode = false;
            $title = "No Resource Box";
        }
        $currentMode = isset($_GET['_resource']) ? $_GET['_resource'] : 'none';
        $res = $mode ? array('_resource' => $mode) : array();
        unset($_GET['_resource']);
        $resourceBoxUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        . '?' . http_build_query(array_merge($_GET, $res));
        $budgeHtml = '<div id="bear_badge">';
        $budgeHtml .= $editHtml;
        if ($hasResource === true) {
            $budgeHtml .= '<a href="' . $resourceBoxUrl . '" class="bear_resource_'
            . $currentMode . '" title="' . $title . '">RES</a>';
        }
        $budgeHtml .= '<a href="/__bear/?id=@@@log_id@@@" class="bear_badge" title="';
        $budgeHtml .= $errorMsg . '" style="background-color:' . $errorBgColor;
        $budgeHtml .= ';color:' . $errorFgColor . ';';
        $budgeHtml .= '">BEAR</a><a href="?_bearinfo" class="bear_info">i</a></div>';
        $budgeHtml = str_replace(
        	'</body>',
        	"$budgeHtml" . '<link rel="stylesheet" href="/__bear/css/debug.css" type="text/css">' . "</body>",
            $html
        );
        return $budgeHtml;
    }

    /**
     * 最後のエラーを取得
     *
     * <pre>
     * _errorクエリーで最後のエラーを表示させます。
     * エラー表示がうまく行かない時に使用します。
     * </pre>
     *
     * <code>
     * ?_error                           エラー表示
     * ?_error=koriyama@bear-project.net エラーメール送信
     * ?_error=/tmp/error.log            エラーログファイルを書き込み
     * </code>
     *
     * @return void
     */
    public static function onShutdownDebug()
    {
        if (function_exists('FB')) {
            $errors = Panda::getOuterPathErrors();
            FB::group('errors', array('Collapsed' => true, 'Color' => 'gray'));
            foreach ($errors as $code => $error) {
                switch (true) {
                    case ($code == E_WARNING || $code == E_USER_WARNING) :
                        $fireLevel = FirePHP::WARN;
                        break;
                    case ($code == E_NOTICE || $code == E_USER_NOTICE) :
                        $fireLevel = FirePHP::INFO;
                        break;
                    case ($code == E_STRICT || $code == E_DEPRECATED) :
                        $fireLevel = FirePHP::LOG;
                        break;
                    default :
                        $fireLevel = FirePHP::ERROR;
                        break;
                }
                FB::send($error, '', $fireLevel);
            }
            FB::groupEnd();
        }
        $lastError = error_get_last();
        $err = print_r($lastError, true);
        if (isset($_GET['_error'])) {
            $errorTo = $_GET['_error'];
            if ($errorTo == '' ) {
                $errorCode = Panda::$phpError[$lastError['type']];
                Panda::error("$errorCode (Last Error)", "{$lastError['message']}", '', (array)$lastError);
                return;
            } elseif (strpos($errorTo, '@')) {
                error_log($err, 1, $errorTo);
            } elseif (is_writable(dirname($errorTo))) {
                error_log("$err\n\n", 3, $errorTo);
            } else {
                echo "<p style=\"color:red\">Error: Invalid destination for _error [$errorTo]</p>";
            }
        }
    }
}