<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Log
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Log.php 1300 2009-12-22 03:37:04Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Log/BEAR_Log.html
 */
/**
 * ログ
 *
 * ログクラスです
 *
 * @category  BEAR
 * @package   BEAR_Log
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Log.php 1300 2009-12-22 03:37:04Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Log/BEAR_Log.html
 */
class BEAR_Log
{

    /**
     * アプリケーションログ
     */
    const TYPE_APP = 0;

    /**
     * エラーログ
     */
    const TYPE_ERROR = 1;

    /**
     * リソースアクセスログ
     */
    const TYPE_RESOURCE = 2;

    /**
     * アプリケーションログ
     *
     * @var array
     */
    private static $_log = array();

    /**
     * ログ
     *
     * <pre>
     * keys
     *
     * 'factory'        factory log
     * 'resource log'   resource input/output log
     * '
     *
     * @var array
     */
    private $_logs = array();

    /**
     * リソースログ
     */
    private $_resourceLog = array();

    /**
     * syslogのlocal番号
     *
     * デフォルトは5
     */
    public static $local = LOG_LOCAL5;

    /**
     * スタティック設定
     */
    private static $_config;

    /**
     * コンストラクタ
     */
    public function __construct(array $config)
    {
        self::$_config = $config;
    }

    /**
     * アプリケーションログを記録
     *
     * <pre>
     * アプリケーションログを記録します。
     * このログは画面上で確認できる一時的なスクリーンログです。
     * </pre>
     *
     * @param string $label ラベル
     * @param mixed  $log   値
     *
     * @return void
     * @access public
     * @static
     */
    public function log($logKey, $logValue = null, $category = 'page')
    {
        if (self::$_config['debug'] !== true) {
        	return;
        }
        if ($category === 'page') {
            self::$_log[][$logKey] = $logValue;
        } else {
            self::$_logs[$category][$logKey] = (array)$logValue;
        }
        if (!is_scalar($logValue)) {
            $logValue = print_r($logValue, true);
            $logValue = str_replace("\n", '', $logValue);
            $logValue = preg_replace("/\s+/s", " ", $logValue);
        }
        if (class_exists('FB', false) && isset($_GET['fire'])) {
            FB::group($logKey);
            FB::log($logValue);
            FB::groupEnd();
        }
        if (PHP_SAPI !== 'cli') {
            error_log("{$logKey} - " . (string)$logValue, 0);
        }
    }

    /**
     * リソースログ
     *
     * <pre>
     * read操作はログには記録されません。
     * </pre>
     *
     * @param string $method メソッド
     * @param string $uri    URI
     * @param array  $values 引数
     *
     * @return void
     *
     * @todo  リソースログコールバック
     */
    public function resourceLog($method, $uri, array $values, $code)
    {
        $this->_resourceLog[] = compact('method', 'uri', 'values', 'code');
        $fullUri = ("{$method} {$uri}") . ($values ? '?' . http_build_query($values) : '') . ' ' . $code;
        $this->log('Resource', $fullUri);
        if ($method == BEAR_Resource::METHOD_READ) {
            return;
        }
        if (is_callable(array('App', 'onCall'))) {
            $result = call_user_func(array('App', 'onCall'), 'resource', array('uri' => $fullUri));
        } else {
            $result = true;
        }
        if ($result !== false) {
            $logger = &Log::singleton('syslog', LOG_USER, 'BEAR RES');
            $logger->log($fullUri);
        }
    }

    /**
     * スクリプトシャットダウン時のログ処理
     *
     * <pre>
     * アプリケーションログ、smartyアサインログ、グローバル変数ログ、
     * リクエストURIをシリアライズしてファイル保存します。
     * デバックモードの時のみ使用します。
     * 保存されたログは/__bear/のLogタブでブラウズできます。
     * シャットダウン時実行のメソッドとしてフレームワーク内で登録され、
     * スクリプト終了時に実行されます。
     * フレームワーク内で使用されます。
     * </pre>
     *
     * @return void
     * @ignore
     *
     */
    public static function onShutDownDebug()
    {
        if (strpos($_SERVER['REQUEST_URI'], '__bear/') !== false){
            return;
        }
        restore_error_handler();
        error_reporting(0);
        try {
            error_reporting(false);
            $isBeardev = isset($_SERVER['__bear']);
            $pageLogPath = _BEAR_APP_HOME . '/logs/' . 'debug' . '.log';
            file_put_contents($pageLogPath, self::$_config['debug']);
            if ($isBeardev || PHP_SAPI === 'cli') {
                return;
            }
            $log = array();
            //　ajaxログ
//            $ajax = BEAR::dependency('BEAR_Page_Ajax');
//            if ($ajax->isAjaxRequest()) {
//                self::_onShutDownDebugAjax();
//                return;
//            }
            $pageLogPath = _BEAR_APP_HOME . '/logs/page.log';
            if (file_exists($pageLogPath) && !is_writable($pageLogPath)) {
                return;
            }
            // page ログ
            $pageLog = file_exists($pageLogPath) ? BEAR_Util::unserialize(file_get_contents($pageLogPath)) : '';
            spl_autoload_unregister(array('BEAR', 'onAutoLoad'));
            //show_vars
            if (!function_exists('show_vars')) {
                include 'Panda/inc/debuglib.php';
            }
            $log['var'] = show_vars('trim_tabs:2;show_objects:1;max_y:100;avoid@:1; return:1');
            if (class_exists('BEAR_Smarty', false)) {
                $smarty = BEAR::dependency('BEAR_Smarty');
                unset($smarty->_tpl_vars['content_for_layout']);
                $log['smarty'] = $smarty->_tpl_vars;
            } else {
                $log['smarty'] = '';
            }
            $oldPageLog = isset($pageLog['page']) ? $pageLog['page'] : array();
            $newPageLog = array('page' => self::$_log,
                'uri' => $_SERVER['REQUEST_URI']);
            $oldPageLog[] = $newPageLog;
            if (count($oldPageLog) > 3) {
                array_shift($oldPageLog);
            }
            $log += array('page' => $oldPageLog,
                'include' => get_included_files(),
                'class' => get_declared_classes());
            if (isset($_SERVER['REQUEST_URI'])) {
                $log += array(
                    'uri' => $_SERVER['REQUEST_URI']);
            }
            $reg = BEAR_Util::getObjectVarsRecursive(BEAR::getAll());
            $log['reg'] = $reg;
            file_put_contents($pageLogPath, serialize($log));
            exit();
        } catch(Exception $e) {
            throw $e;
        }
    }


    /**
     * AJAX終了処理
     *
     * ajax.logをlogフォルダに作成する
     *
     * @return void
     */
    private static function _onShutDownDebugAjax()
    {
        $ajaxLogPath = _BEAR_APP_HOME . '/logs/ajax.log';
        $ajaxLog = file_exists($ajaxLogPath) ? BEAR_Util::unserialize(file_get_contents($ajaxLogPath)) : null;
        $log = array('page' => self::$_log, 'uri' => $_SERVER['REQUEST_URI']);
        $ajaxLog[] = $log;
        if (count($ajaxLog) > 5) {
            array_shift($ajaxLog);
        }
        file_put_contents(_BEAR_APP_HOME . '/logs/ajax.log', serialize($ajaxLog));
    }

    /**
     * スクリプトシャットダウン時のログ処理
     *
     * @return void
     */
    public static function onShutDownLive()
    {
    }
}
