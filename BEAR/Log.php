<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * ログ
 *
 * 開発時のログを扱います
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 *
 * @Singleton
 */
class BEAR_Log extends BEAR_Base
{
    /**
     * アプリケーションログ
     *
     * @var array
     */
    private $_logs = array();

    /**
     * テンポラリーログ記録開始オフセット
     *
     * @var int
     */
    private $_temporaryOffset = 0;

    /**
     * リソースログ
     *
     * @var array
     */
    private $_resourceLog = array();

    /**
     * FirePHP表示もするログキー
     *
     * @var string
     */
    private $_fbKeys = array('onInit');

    /**
     * アプリケーションログを記録
     *
     * <pre>
     * アプリケーションログを記録します。
     * このログは画面上で確認できる一時的なスクリーンログです。
     * </pre>
     *
     * @param string $logKey   ログキー
     * @param mixed  $logValue 値
     */
    public function log($logKey, $logValue = null)
    {
        if (! isset($this->_config['debug']) || $this->_config['debug'] !== true) {
            return;
        }
        $this->_logs[][$logKey] = $logValue;
        $showFirePHP = (isset($_GET['_firelog']) || array_search($logKey, $this->_fbKeys, true) !== false);
        if (class_exists('FB', false) && $showFirePHP) {
            $color = ($logValue) ? 'black' : 'grey';
            FB::group($logKey, array('Collapsed' => true, 'Color' => $color));
            FB::log($logValue);
            FB::groupEnd();
        }
        if (! is_scalar($logValue)) {
            $logValue = print_r($logValue, true);
            $logValue = str_replace("\n", '', $logValue);
            $logValue = preg_replace("/\s+/s", ' ', $logValue);
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
     * @param int    $code   コード
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
     * shutdown関数から呼ばれます
     */
    public static function onShutdownDebug()
    {
        $bearLog = BEAR::dependency('BEAR_Log');
        if (class_exists('SQLiteDatabase', false)) {
            $bearLog->shutdonwnDbDebug();
        } else {
            $bearLog->shutdownDebug(false);
            $ob = ob_get_clean();
            $ob = str_replace('?id=@@@log_id@@@', '?nosqlite', $ob);
            echo $ob;
        }
    }

    /**
     * Write page log onto DB on shutdown
     */
    public function shutdonwnDbDebug()
    {
        $db = $this->getPageLogDb();
        $log = $this->shutdownDebug();
        $log = sqlite_escape_string(serialize($log));
        $sql = "INSERT INTO pagelog(log) VALUES('{$log}')";
        $db->queryExec($sql);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $id = $db->lastInsertRowid();
        $ob = ob_get_clean();
        $ob = str_replace('@@@log_id@@@', $id, $ob);
        echo $ob;
        // keep only
        $db->query('DELETE FROM pagelog WHERE rowid IN (SELECT rowid FROM pagelog ORDER BY rowid LIMIT -1 OFFSET 100');
    }

    /**
     * Get log db
     *
     * @return SQLiteDatabase
     */
    public function getPageLogDb()
    {
        $file = _BEAR_APP_HOME . '/logs/pagelog.sq3';
        if (file_exists($file) === false) {
            $db = new SQLiteDatabase($file);
            $sql = <<<'____SQL'
CREATE TABLE pagelog (
     "log" text NOT NULL
);
____SQL;
            $db->queryExec($sql);
        } else {
            $db = new SQLiteDatabase($file);
        }
        if ($db === false) {
            throw new BEAR_Exception('sqlite error');
        }

        return $db;
    }

    /**
     * Get page log
     *
     * @param array $get
     *
     * @return array|mixed|string
     */
    public function getPageLog(array $get)
    {
        if (! class_exists('SQLiteDatabase', false)) {
            $pageLogPath = _BEAR_APP_HOME . '/logs/page.log';
            include_once 'BEAR/Util.php';
            $pageLog = file_exists($pageLogPath) ? BEAR_Util::unserialize(file_get_contents($pageLogPath)) : array();

            return $pageLog;
        }
        $db = $this->getPageLogDb();
        if (isset($get['id'])) {
            //    $rowid = sqlite
            $rowid = sqlite_escape_string($get['id']);
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $result = $db->query("SELECT log FROM pagelog WHERE rowid = {$rowid}");
        } else {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $result = $db->query('SELECT log FROM pagelog ORDER BY rowid DESC LIMIT 1');
        }
        if ($result === false) {
            die('Log db is not avalilabe.');
        }
        $log = $result->fetchAll();
        $pageLog = unserialize($log[0]['log']);

        return $pageLog;
    }

    /**
     * スクリプトシャットダウン時のログ処理
     *
     * アプリケーションログ、smartyアサインログ、グローバル変数ログ、
     * リクエストURIをシリアライズしてファイル保存します。
     * デバックモードの時のみ使用します。
     * 保存されたログは/__bear/のLogタブでブラウズできます。
     * シャットダウン時実行のメソッドとしてフレームワーク内で登録され、
     * スクリプト終了時に実行されます。
     * フレームワーク内で使用されます。
     *
     * @param bool $return
     *
     * @throws Exception
     *
     * @return array
     */
    public function shutdownDebug($return = true)
    {
        if (PHP_SAPI === 'cli') {
            return;
        }
        if (strpos($_SERVER['REQUEST_URI'], '__bear/') !== false) {
            return;
        }
        restore_error_handler();
        error_reporting(0);
        try {
            $isBeardev = isset($_SERVER['__bear']);
            $pageLogPath = _BEAR_APP_HOME . '/logs/' . 'debug' . '.log';
            file_put_contents($pageLogPath, $this->_config['debug']);
            if ($isBeardev || PHP_SAPI === 'cli') {
                return;
            }
            $log = array();
            $pageLogPath = _BEAR_APP_HOME . '/logs/page.log';
            if (file_exists($pageLogPath) && ! is_writable($pageLogPath)) {
                // 書き込み権限のエラー
                Panda::error('Permission denied.', "[$pageLogPath] is not writable.");

                return;
            }
            // page ログ
            $pageLog = file_exists($pageLogPath) ? BEAR_Util::unserialize(file_get_contents($pageLogPath)) : '';
            // show_vars
            if (! function_exists('show_vars')) {
                include 'BEAR/vendors/debuglib.php';
            }
            $log['var'] = show_vars('trim_tabs:2;show_objects:1;max_y:100;avoid@:1; return:1');
            if (BEAR::exists('BEAR_Smarty')) {
                $smarty = BEAR::dependency('BEAR_Smarty');
                $log['smarty'] = $smarty->get_template_vars();
                unset($log['smarty']['content_for_layout']);
            } else {
                $log['smarty'] = '';
            }
            $oldPageLog = isset($pageLog['page']) ? $pageLog['page'] : array();
            $newPageLog = array(
                'page' => $this->_logs,
                'uri' => $_SERVER['REQUEST_URI']
            );
            $oldPageLog[] = $newPageLog;
            if (count($oldPageLog) > 3) {
                array_shift($oldPageLog);
            }
            $log += array(
                'page' => $oldPageLog,
                'include' => get_included_files(),
                'class' => get_declared_classes()
            );
            if (isset($_SERVER['REQUEST_URI'])) {
                $log += array(
                    'uri' => $_SERVER['REQUEST_URI']
                );
            }
            $reg = BEAR_Util::getObjectVarsRecursive(BEAR::getAll());
            $log['reg'] = $reg;
            if ($return === true) {
                return $log;
            }
            file_put_contents($pageLogPath, serialize($log));
        } catch (Exception $e) {
            throw $e;
        }
    }

    //    /**
    //     * AJAX終了処理
    //     *
    //     * ajax.logをlogフォルダに作成する
    //     *
    //     * @return void
    //     */
    //    private function _onShutdownDebugAjax()
    //    {
    //        $ajaxLogPath = _BEAR_APP_HOME . '/logs/ajax.log';
    //        $ajaxLog = file_exists($ajaxLogPath) ? BEAR_Util::unserialize(file_get_contents($ajaxLogPath)) : null;
    //        $log = array('page' => $this->_logs, 'uri' => $_SERVER['REQUEST_URI']);
    //        $ajaxLog[] = $log;
    //        if (count($ajaxLog) > 5) {
    //            array_shift($ajaxLog);
    //        }
    //        file_put_contents(_BEAR_APP_HOME . '/logs/ajax.log', serialize($ajaxLog));
    //    }

    /**
     * ログを記録開始
     */
    public function start()
    {
        $this->_temporaryOffset = count($this->_logs);
    }

    /**
     * ログを記録開始
     *
     * @return mixed
     */
    public function stop()
    {
        $length = count($this->_logs) - $this->_temporaryOffset;
        $result = array_slice($this->_logs, $this->_temporaryOffset, $length);

        return $result;
    }
}
