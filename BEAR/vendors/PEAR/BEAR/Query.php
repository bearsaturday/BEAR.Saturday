<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Query
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Query.php 1250 2009-12-06 17:03:41Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
/**
 * クエリークラス
 *
 * <pre>
 * select, insert, update, deleteのクエリーツールです。
 * selectにはHTMLリンクも伴うDBページング機能、他の文にはSQL生成の機能が付加されています。
 * MDB2を使用していますが、クライアントはMDB2の実装に依存しないでBEAR_Queryのインターフェイスに依存できます。
 *
 * Example 1. セレクトクエリー
 *
 * </pre>
 * <code>
 *   $config = array('db'=>$db, 'table'=>$table, 'pager'=>false, 'pager_option'=> $pagerOption);
 *   $query = BEAR_Dependency('BEAR_Query, $config);
 *   // 直接select
 *   $sql = "SELECT * FROM users WHERE id = 1";
 *   $result = $query->select($sql);
 *   // プリペアードステートメント (quote自動）
 *   $sql = "SELECT * FROM users WHERE id = :id";
 *   $params = array('id'=>1);
 *   $result = $query->select($sql, $params);
 *   // insert
 *   $values = array('name'=>'bear', 'age'=>10);
 *   $result = $query->insert($values);
 *   // update
 *   $values = array('name'=>'bear', 'age'=>10);
 *   $where = 'id = '. $query->quote($id, 'integer');
 *   $result = $query->$update($values, $where);
 *   // テーブル指定update
 *   $table = 'another_user';
 *   $result = $query->$update($values, $where, $table);
 * </code>
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
 * @package   BEAR_Query
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Query.php 1250 2009-12-06 17:03:41Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
class BEAR_Query extends BEAR_Base implements BEAR_Query_Interface
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
     * セレクト
     *
     * <pre>
     * 通常のselect文の他にDB結果の一部だけをSELECTする機能と、HTMLページングの機能が合わさった
     * メソッドです。getAll()メソッドの引数に加えて一画面に表示するデータ数を
     * 引数に指示するとページング(スライス）されたデータ結果、と
     * エージェントに合わせたリンクHTML文字列が返ります。
     *
     * $paramsが空だと通常のSQL、連想配列が入っていると$queryをpreparedステートメート文として期待して実行します。
     * </pre>
     *
     * @param string $query  SQL
     * @param array  $params プリペアードステートメントにする場合にバインドする変数
     * @param array  $values arrayなら
     *
     * @return BEAR_Ro
     */
    public function select($query, array $params = array(), array $values = null, $id = 'id')
    {
        assert(is_object($this->_config['db']));
        assert(is_object($this->_config['ro']));
        $db = &$this->_config['db'];
        $ro = $this->_config['ro'];
        if ($this->_config['debug']) {
            //SQLとリソースオブジェクトを対にするコメントクエリー
            $cmt =  '# RO=' . get_class($ro);
            $db->query($cmt);
        }
        // Row取得
        if (!is_null($values)) {
            $result =  $this->_selectRow($db, $query, $params, $values, $id);
            if ($result !== false) {
                return $result;
            }
        }
        // All取得
        // 論理削除
        if (isset($this->_config['deleted_at']) && $this->_config['deleted_at'] === true) {
            if (stripos($query, 'WHERE') === false){
                $query .= ' WHERE deleted_at IS NULL';
            } else {
                $query = str_ireplace('WHERE', 'WHERE deleted_at IS NULL AND', $query);
            }
        }
        // ソート
        if (isset($_GET['_sort']) && isset($this->_config['sort'])) {
            $query .= $this->_sort();
        }
        if ((!isset($this->_config['pager']) || !$this->_config['pager']) || $this->_config['perPage'] <= 0) {
            // LIMIT
            if (isset($this->_config['perPage']) && $this->_config['perPage'] > 0) {
                $query .= ' LIMIT ' . $this->_config['perPage'];
            }
            if ($params) {
                $sth = $db->prepare($query);
                $result = $sth->execute($params)->fetchAll();
            } else {
                $result = $db->queryAll($query);
            }
            return $result;
        }
        // DBページャー
        $pagerOptions = $this->_config['options'];
        $pagerOptions['perPage'] = $this->_config['perPage'];
        if (!array_key_exists('totalItems', $pagerOptions)) {
            $pagerOptions['totalItems'] = $this->_countQuery($query, $params);
        }
        // ページング
        $pager = BEAR::dependency('BEAR_Pager');
        /* @var $pager BEAR_Pager */
        // totalItems以外のBEAR_Pagerデフォルトオプションを使用
        $defaultPagerOptions = $pager->getPagerOptions();
        unset($defaultPagerOptions['totalItems']);
        $pagerOptions = $pagerOptions + $defaultPagerOptions;
        $pager->setOptions($pagerOptions);
        $pager->pager->build();
        //情報
        $info['totalItems'] = $pagerOptions['totalItems'];
        $pager->makeLinks($pagerOptions['delta'], $pagerOptions['totalItems']);
        $links = $pager->pager->getLinks();
        $info['page_numbers'] = array('current' => $pager->pager->getCurrentPageID(),
            'total' => $pager->pager->numPages());
        list($info['from'], $info['to']) = $pager->pager->getOffsetByPageId();
        $info['limit'] = $info['to'] - $info['from'] + 1;
        $db->setLimit($pagerOptions['perPage'], $info['from'] - 1);
        if ($params) {
            $sth = $db->prepare($query);
            $result = $sth->execute($params)->fetchAll();
        } else {
            $result = $db->queryAll($query);
        }
        if (PEAR::isError($result)) {
            return $result;
        }
        // ROオブジェクトで返す
        /* @var $ro BEAR_Ro */
        $ro->setBody($result);
        $ro->setHeaders($info);
        $pager = array('pager'=>$links);
        $ro->setLinks($pager);
        $log = BEAR::dependency('BEAR_Log');
        /* @var $log BEAR_Log */
        $log->log('DB Pager', $info);
        return $ro;
    }

    /**
     * Rowセレクト
     *
     * $values配列に$idをキーにした変数があれば（単数、複数）それを=の条件としてwhereを作成してクエリーをRow取得する
     *
     * <pre>
     * example.
     *
     * $values = array('id'=>5)
     * $id = 'id';
     *
     * だと"WHERE id = '5'"のクエリーが生成される
     *
     * $values = array('id'=>5, 'delete_flg'=>1)
     * $id = array('id', 'delete_flg');
     *
     * だと"WHERE id = '5' AND delete_flg = '1'"のクエリーが生成される
     *
     * @param object $db
     * @param string $query
     * @param array  $params
     * @param array  $values
     * @param mixed  $id
     *
     * @return mixed
     */
    private function _selectRow(&$db, $query, $params, $values, $id)
    {
        $where = array();
        if (is_string($id) && array_key_exists($id, $values)) {
            $where[] = $id . ' = ' .$values[$id] ;
        } elseif (is_array($id) && (count(array_intersect($id, array_keys($values))) === count($id))) {
            foreach ($id as &$key) {
                $where[] = $key . ' = \'' .$values[$key] . '\'';
            }
        } else {
            return false;
        }
        if ($this->_config['deleted_at']) {
            $where[] = 'deleted_at IS NULL';
        }
        // where条件結合
        if ($where) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        if ($params) {
            $sth = $db->prepare($query);
            $result = $sth->execute($params)->fetchRow();
        } else {
            $result = $db->queryRow($query);
        }
        return $result;
    }

    /**
     * ソート
     *
     * クエリーからsort文を作成します。
     * <pre>
     * exmaple 1.
     * ?_sort=id
     *
     * ORDER BY ID
     *
     * example 2.
     * ?_sort=id,-flg
     *
     * ORDER BY ID, FLG DESC
     * </pre>
     *
     * @return string
     */
    private function _sort() {
        $sortCmd = explode(',', $_GET['_sort']);
        $orders = array();
        foreach ($sortCmd as $item) {
            $order = 'ASC';
            if (substr($item, 0 ,1) === '-') {
                $item = substr($item, 1);
                $order = 'DESC';
            }
            // valid key ? (in config ?)
            $search = array_search($item, $this->_config['sort']);
            if ($search !== false) {
                $item = is_string($search) ? $search : $item ;
                $orders[] = '`' . $item . '`'. ' ' . $order;
            }
        }
        $result = ($orders) ? ' ORDER BY ' .  implode(',', $orders) : '';
        return $result;
    }

    /**
     * インサート
     *
     * @return mixeds
     */
    public function insert(array $values, $table = null)
    {
        $db = &$this->_config['db'];
        $table = $table ? $table : $this->_config['table'];
        $affectedRow = $db->extended->autoExecute($table, $values, MDB2_AUTOQUERY_INSERT);
        return $affectedRow;
    }

    /**
     * アップデート
     *
     * @return mixed
     */
    public function update(array $values, $where, $table = null)
    {
        $db = &$this->_config['db'];
        $table = $table ? $table : $this->_config['table'];
        $affectedRow = $db->extended->autoExecute($table, $values, MDB2_AUTOQUERY_UPDATE, $where);
        return $affectedRow;
    }

    /**
     * デリート
     *
     * @return mixed
     */
    public function delete($where, $table = null)
    {
        $db = &$this->_config['db'];
        $table = $table ? $table : $this->_config['table'];
        $affectedRow = $db->extended->autoExecute($table, null, MDB2_AUTOQUERY_DELETE, $where);
        return $affectedRow;
    }

    /**
     * クオート
     *
     * @return strings
     */
    public function quote($value, $type)
    {
        return $this->_config['db']->quote($value, $type);
    }

    /**
     * エラー？
     *
     * @param MDB2_Result $result DB結果
     *
     * @return mixed
     */
    public function isError($result)
    {
        return PEAR::isError($result);
    }

    public function count()
    {
    }

    protected function _countQuery($query, $params = array())
    {
        //be smart and try to guess the total number of records
        $countQuery = $this->_rewriteCountQuery($query);
        if ($countQuery) {
            if ($params) {
                $sth = $this->_config['db']->prepare($countQuery);
                $totalItems = $sth->execute($params)->fetchOne();
            } else {
                $totalItems = $this->_config['db']->queryOne($countQuery);
            }
            if (PEAR::isError($totalItems)) {
                return $totalItems;
            }
        } else {
            //GROUP BY => fetch the whole resultset and count the rows returned
            $res = $this->_config['db']->queryCol($query);
            if (PEAR::isError($res)) {
                return $res;
            }
            $totalItems = count($res);
        }
        return $totalItems;
    }

    /**
     * カウントクエリーを生成する
     *
     * <pre>SQL文から"SELECT COUNT(*)" を付加したカウントクエリーを生成して返します。
     * COUNT文を含まないSQLからセレクト結果の個数を知るためCOUNTのクエリーを使用するのに用います
     * DBページャーで内部的にも使用されています。
     *
     * Example 1. SQLからCOUNT()を取得
     *
     * </pre>
     * <code>
     * $count_query = $this->getCountSQL($query));
     * $total_items = $this->getOne($count_query, $params);
     * </code>
     *
     * @param string $query クエリー
     *
     * @return string 書き換えられたクエリー | false（失敗
     * @static
     *
     */
    private function _rewriteCountQuery($query)
    {
        if (preg_match('/^\s*SELECT\s+\bDISTINCT\b/is', $query) || preg_match('/\s+GROUP\s+BY\s+/is', $query)) {
            return false;
        }
        $openParenthesis = '(?:\()';
        $closeParenthesis = '(?:\))';
        $subqueryInSelect = $openParenthesis . '.*\bFROM\b.*' . $closeParenthesis;
        $pattern = '/(?:.*' . $subqueryInSelect . '.*)\bFROM\b\s+/Uims';
        if (preg_match($pattern, $query)) {
            return false;
        }
        $subqueryWithLimitOrder = $openParenthesis . '.*\b(LIMIT|ORDER)\b.*' . $closeParenthesis;
        $pattern = '/.*\bFROM\b.*(?:.*' . $subqueryWithLimitOrder . '.*).*/Uims';
        if (preg_match($pattern, $query)) {
            return false;
        }
        $queryCount = preg_replace('/(?:.*)\bFROM\b\s+/Uims', 'SELECT COUNT(*) FROM ', $query, 1);
        list($queryCount, ) = preg_split('/\s+ORDER\s+BY\s+/is', $queryCount);
        list($queryCount, ) = preg_split('/\bLIMIT\b/is', $queryCount);
        return trim($queryCount);
    }


}