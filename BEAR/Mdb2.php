<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * MDB2
 *
 * PEAR::MDB2を生成、設定してインスタンスを返すMDB2のファクトリークラスです。
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
 * MDB2オプション
 * </pre>
 * <ul>
 *  <li>$options['ssl'] -> boolean: determines if ssl should be used for connections</li>
 *  <li>$options['field_case'] -> CASE_LOWER|CASE_UPPER: determines what case to force on field/table names</li>
 *  <li>$options['disable_query'] -> boolean: determines if queries should be executed</li>
 *  <li>$options['result_class'] -> string: class used for result sets</li>
 *  <li>$options['buffered_result_class'] -> string: class used for buffered result sets</li>
 *  <li>$options['result_wrap_class'] -> string: class used to wrap result sets into</li>
 *  <li>$options['result_buffering'] -> boolean should results be buffered or not?</li>
 *  <li>$options['fetch_class'] -> string: class to use when fetch mode object is used</li>
 *  <li>$options['persistent'] -> boolean: persistent connection?</li>
 *  <li>$options['debug'] -> integer: numeric debug level</li>
 *  <li>$options['debug_handler'] -> string: function/method that captures debug messages</li>
 *  <li>$options['debug_expanded_output'] -> bool: BC option to determine if more context information should be send to the debug handler</li>
 *  <li>$options['default_text_field_length'] -> integer: default text field length to use</li>
 *  <li>$options['lob_buffer_length'] -> integer: LOB buffer length</li>
 *  <li>$options['log_line_break'] -> string: line-break format</li>
 *  <li>$options['idxname_format'] -> string: pattern for index name</li>
 *  <li>$options['seqname_format'] -> string: pattern for sequence name</li>
 *  <li>$options['savepoint_format'] -> string: pattern for auto generated savepoint names</li>
 *  <li>$options['statement_format'] -> string: pattern for prepared statement names</li>
 *  <li>$options['seqcol_name'] -> string: sequence column name</li>
 *  <li>$options['quote_identifier'] -> boolean: if identifier quoting should be done when check_option is used</li>
 *  <li>$options['use_transactions'] -> boolean: if transaction use should be enabled</li>
 *  <li>$options['decimal_places'] -> integer: number of decimal places to handle</li>
 *  <li>$options['portability'] -> integer: portability constant</li>
 *  <li>$options['modules'] -> array: short to long module name mapping for __call()</li>
 *  <li>$options['emulate_prepared'] -> boolean: force prepared statements to be emulated</li>
 *  <li>$options['datatype_map'] -> array: map user defined datatypes to other primitive datatypes</li>
 *  <li>$options['datatype_map_callback'] -> array: callback function/method that should be called</li>
 *  <li>$options['bindname_format'] -> string: regular expression pattern for named parameters</li>
 *  <li>$options['multi_query'] -> boolean: determines if queries returning multiple result sets should be executed</li>
 *  <li>$options['max_identifiers_length'] -> integer: max identifier length</li>
 *  <li>$options['default_fk_action_onupdate'] -> string: default FOREIGN KEY ON UPDATE action ['RESTRICT'|'NO ACTION'|'SET DEFAULT'|'SET NULL'|'CASCADE']</li>
 *  <li>$options['default_fk_action_ondelete'] -> string: default FOREIGN KEY ON DELETE action ['RESTRICT'|'NO ACTION'|'SET DEFAULT'|'SET NULL'|'CASCADE']</li>
 * </ul>
 *
 * @instance prototype
 *
 * @config string dsn     DSN          *required
 * @config array  options MDB2オプション array()
 *
 * @Singleton
 */
class BEAR_Mdb2 extends BEAR_Factory
{
    /**
     * Las log
     *
     * @var array
     */
    private static $_lastLog = [];

    /**
     * Constructor
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * ファクトリー
     *
     * MDB2を生成して設定（エラーハンドラーの設定やフェッチモードをASSOC）しています。
     * $dsnを省略すればApp_DB::$config['db']['default']がDSNとして利用されます。
     *
     * @throws BEAR_Mdb2_Exception
     *
     * @return MDB2_Driver_Datatype_mysqli
     */
    public function factory()
    {
        static $_instance;

        if (isset($_instance[$this->_config['dsn']])) {
            return $_instance[$this->_config['dsn']];
        }
        $options = (isset($this->_config['options']) && is_array(
            $this->_config['options']
        )) ? $this->_config['options'] : [];
        // MDB2インスタンス生成
        if ($this->_config['debug']) {
            // Debugモード オプション
            $options['debug'] = true;
            $options['debug_handler'] = ['BEAR_Mdb2', 'onDebug'];
        } else {
            $options['debug'] = false;
        }
        $mdb2 = MDB2::factory($this->_config['dsn'], $options);
        if (PEAR::isError($mdb2)) {
            throw $this->_exception('db connection error', ['code' => 503 , 'info' => ['dsn' => $this->_config['dsn']]]);
        }
        $mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
        $_instance[$this->_config['dsn']] = $mdb2;

        return $mdb2;
    }

    //    /**
    //     * エラーハンドラー
    //     *
    //     * エラー処理
    //     *
    //     * @param object $paerError PEARエラー
    //     *
    //     * @return void
    //     * @ignore
    //     * @static
    //     */
    //    private static function _errorHandler($paerError)
    //    {
    //        trigger_error('BEAR_Mdb2 Error:' . $paerError->toString(), E_USER_WARNING);
    //    }

    /**
     * デバック用ハンドラ
     *
     * @param object &$db     MDB2オブジェクト
     * @param string $scope   スコープ
     * @param string $message メッセージ
     * @param bool   $isManip 不明
     */
    public static function onDebug(&$db, $scope, $message, $isManip = null)
    {
        if (substr($message, 0, 1) === '#') {
            return;
        }
        $log['scope'] = $scope;
        $log['message'] = $message;
        if ($scope == 'query') {
            $log['message'] = $message . $db->getOption('log_line_break');
        }
        if ($isManip !== null) {
            $log['isManip'] = $isManip;
        }
        /** @var BEAR_Log $bearLog */
        $bearLog = BEAR::dependency('BEAR_Log');
        self::$_lastLog[] = $log;
        $bearLog->log('Mdb2', $log);
    }
}
