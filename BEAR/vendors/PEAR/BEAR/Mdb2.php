<?php
/**
 * BEAR_Mdb2
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Mdb2
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Mdb2.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
/**
 * MDB2クラス
 *
 * <pre>
 * PEAR::MDB2を生成、設定してインスタンスを返すMDB2のファクトリークラスです。
 * </pre>
 *
 * <pre>
 * MDB2結果コード
 *
 * ('MDB2_OK',                      true);
 * ('MDB2_ERROR',                     -1);
 * ('MDB2_ERROR_SYNTAX',              -2);
 * ('MDB2_ERROR_CONSTRAINT',          -3);
 * ('MDB2_ERROR_NOT_FOUND',           -4);
 * ('MDB2_ERROR_ALREADY_EXISTS',      -5);
 * ('MDB2_ERROR_UNSUPPORTED',         -6);
 * ('MDB2_ERROR_MISMATCH',            -7);
 * ('MDB2_ERROR_INVALID',             -8);
 * ('MDB2_ERROR_NOT_CAPABLE',         -9);
 * ('MDB2_ERROR_TRUNCATED',          -10);
 * ('MDB2_ERROR_INVALID_NUMBER',     -11);
 * ('MDB2_ERROR_INVALID_DATE',       -12);
 * ('MDB2_ERROR_DIVZERO',            -13);
 * ('MDB2_ERROR_NODBSELECTED',       -14);
 * ('MDB2_ERROR_CANNOT_CREATE',      -15);
 * ('MDB2_ERROR_CANNOT_DELETE',      -16);
 * ('MDB2_ERROR_CANNOT_DROP',        -17);
 * ('MDB2_ERROR_NOSUCHTABLE',        -18);
 * ('MDB2_ERROR_NOSUCHFIELD',        -19);
 * ('MDB2_ERROR_NEED_MORE_DATA',     -20);
 * ('MDB2_ERROR_NOT_LOCKED',         -21);
 * ('MDB2_ERROR_VALUE_COUNT_ON_ROW', -22);
 * ('MDB2_ERROR_INVALID_DSN',        -23);
 * ('MDB2_ERROR_CONNECT_FAILED',     -24);
 * ('MDB2_ERROR_EXTENSION_NOT_FOUND',-25);
 * ('MDB2_ERROR_NOSUCHDB',           -26);
 * ('MDB2_ERROR_ACCESS_VIOLATION',   -27);
 * ('MDB2_ERROR_CANNOT_REPLACE',     -28);
 * ('MDB2_ERROR_CONSTRAINT_NOT_NULL',-29);
 * ('MDB2_ERROR_DEADLOCK',           -30);
 * ('MDB2_ERROR_CANNOT_ALTER',       -31);
 * ('MDB2_ERROR_MANAGER',            -32);
 * ('MDB2_ERROR_MANAGER_PARSE',      -33);
 * ('MDB2_ERROR_LOADMODULE',         -34);
 * ('MDB2_ERROR_INSUFFICIENT_DATA',  -35);
 *
 * DATAタイプ
 *
 * 'text':
 * 'clob':
 * 'blob':
 * 'integer':
 * 'boolean':
 * 'date':
 * 'time':
 * 'timestamp':
 * 'float':
 * 'decimal':
 *
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Mdb2
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Mdb2.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
class BEAR_Mdb2 extends BEAR_Factory
{

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
     * シングルトン
     *
     * <pre>
     * MDB2のsingletonメソッドに相当する昨日に加えてエラーハンドラーの設定や
     * フェッチモードをASSOCにしています。
     * $dsnを省略しればApp_DB::$config['db']['default']がDSNとして利用されます。
     *　</pre>
     *
     * @param string $dsn     DSN
     * @param array  $options オプション
     *
     * @return MDB2_Driver_Datatype_mysqli
     */
    public function factory()
    {
        $options = (isset($this->_config['options']) && is_array($this->_config['options'])) ? $this->_config['options'] : array();
        // MDB2インスタンス生成
        if ($this->_config['debug']) {
            // Debugモード オプション
            $options['debug'] = true;
            $options['debug_handler'] = array('BEAR_Mdb2', 'onDebug');
        } else {
            $options['debug'] = false;
        }
        $mdb2 = MDB2::factory($this->_config['dsn'], $options);
        if (PEAR::isError($mdb2)) {
            var_export($this->_config['dsn']);
            exit();
            throw $this->_exception('db connection error', 503, array(
                'dsn' => $this->_config['dsn']));
        }
        $mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
        if ($this->_config['debug'] === true) {
            $mdb2->query('# --------------------------- BEAR Debug Mode ' . $this->_config['info']['id'] . ' ' . $this->_config['info']['version'] . '---------------------------');
        }
        return $mdb2;
    }


    /**
     * エラーハンドラー
     *
     * エラー処理
     *
     * @param object $paerError PEARエラー
     *
     * @return void
     * @ignore
     * @static
     */
    private static function _errorHandler($paerError)
    {
        trigger_error('BEAR_Mdb2 Error:' . $paerError->toString(), E_USER_WARNING);
    }

    /**
     * デバック用ハンドラ
     *
     * @param object       &$db     MDB2オブジェクト
     * @param string       $scope   スコープ
     * @param string       $message メッセージ
     * @param unknown_type $isManip 不明
     *
     * @return void
     */
    public static function onDebug(&$db, $scope, $message, $isManip = null)
    {
    	if (substr($message, 0, 1) === '#'){
    		return;
    	}
        $log['scope'] = $scope;
        $log['message'] = $message;
        if ($scope == 'query') {
            $log['message'] = $message . $db->getOption('log_line_break');
        }
        if (!is_null($isManip)) {
            $log['isManip'] = $isManip;
        }
        $bearLog = BEAR::dependency('BEAR_Log');
        $bearLog->log('MDB2', $log);
    }
}
