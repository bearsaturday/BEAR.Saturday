<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Query
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: 0.9.15 $Id: Query.php 2552 2011-06-15 07:13:09Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * クエリークラス
 *
 * <pre>
 * selectを使用した場合,preparedステートメントを毎回freeします。
 * selectMultipleを使用する場合は、Roクラス内でBEAR::dependencyを使用して呼び出してください。
 * </pre>
 */
class Bear_Query_Free extends BEAR_Query
{
    //prepareステートメント結果を使いまわすために使用
    protected $_stmt = null;
    protected $_count_stmt = null;
    
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }
    
    /**
    * プリペアを使い回すセレクト
    *
    * <pre>
    * 通常のselect文の他にDB結果の一部だけをSELECTする機能と、HTMLページングの機能が合わさった
    * メソッドです。getAll()メソッドの引数に加えて一画面に表示するデータ数を
    * 引数に指示するとページング(スライス）されたデータ結果と
    * エージェントに合わせたリンクHTML文字列が返ります。
    * $valuesが配列<array($key => $values)>ならWHERE $key1 = $id1 AND $key2 = $id2 ..と条件を作ってselectします。
    * リソース内で受け取った$valuesを条件にSELETするときに使います。
    *
    * $paramsが空だと通常のSQL、連想配列が入っていると$queryをpreparedステートメート文として期待して実行します。
    * </pre>
    *
    * @param string $query  SQL
    * @param array  $params プリペアードステートメントにする場合にバインドする変数
    * @param array  $values where条件配列
    * @param string $id
    * @param boolean $free  プリペアードステートメントをfreeする時true
    *
    * @return BEAR_Ro
    */
    public function selectSharePrepare($query, array $params = array(), array $values = null, $id = 'id', $free=false)
    {
        assert(is_object($this->_config['db']));
        assert(is_object($this->_config['ro']));
        $db = &$this->_config['db'];
        $ro = $this->_config['ro'];
        $sth = null;
        
        // Row取得
        if (!is_null($values)) {
            if(is_object($this->_stmt)) {
                $sth = $this->_stmt;
            }
            $result = $this->_selectRow($db, $query, $params, $values, $id, $sth);
            if(!is_object($sth) && is_null($this->_stmt)) {
                $this->_stmt = $sth;
            }
            if($free) {
                $this->_stmt->free();
                $this->_stmt = null;
            }
            if ($result !== false) {
                return $result;
            }
        }
        // All取得
        // 論理削除
        if (isset($this->_config['deleted_at']) && $this->_config['deleted_at'] === true
        ) {
            if (stripos($query, 'WHERE') === false) {
                $query .= ' WHERE deleted_at IS NULL';
            } else {
                $query = str_ireplace('WHERE', 'WHERE deleted_at IS NULL AND', $query);
            }
        }
        // ソート
        if (isset($this->_config['sort'])) {
            $query = $this->_sort($query);
        }
        if ((!isset($this->_config['pager']) || !$this->_config['pager']) || $this->_config['perPage'] <= 0) {
            if (isset($this->_config['offset']) && $this->_config['perPage'] > 0) {
                // LIMIT & Offset
                $query .= ' LIMIT ' . $this->_config['offset'] . ',' . $this->_config['perPage'];
            } elseif (isset($this->_config['perPage']) && $this->_config['perPage'] > 0) {
                // LIMIT
                $query .= ' LIMIT ' . $this->_config['perPage'];
            }
            if ($params) {
                if(!is_object($sth) && is_null($this->_stmt)) {
                    $sth = $db->prepare($query);
                    $this->_stmt = $sth;
                } else {
                    $sth = $this->_stmt;
                }
                if($free) {
                    $this->_stmt->free();
                    $this->_stmt = null;
                }
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
            if(is_object($this->_stmt)) {
                $count_sth = $this->_count_stmt;
            }
            $pagerOptions['totalItems'] = $this->_countQuery($query, $params,$count_sth);
            if(!is_object($count_sth) && is_null($this->_count_stmt)) {
                $this->_count_stmt = $count_sth;
            }
            if($free) {
                $this->_count_stmt->free();
                $this->_count_stmt = null;
            }
        }
        // ページング
        $pager = BEAR::dependency('BEAR_Pager');
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
        $info['page_numbers'] = array(
                'current' => $pager->pager->getCurrentPageID(),
                'total' => $pager->pager->numPages()
        );
        list($info['from'], $info['to']) = $pager->pager->getOffsetByPageId();
        $info['limit'] = $info['to'] - $info['from'] + 1;
        $db->setLimit($pagerOptions['perPage'], $info['from'] - 1);
        if ($params) {
            if(!is_object($sth) && is_null($this->_stmt)) {
                $sth = $db->prepare($query);
                $this->_stmt = $sth;
            } else {
                $sth = $this->_stmt;
            }
            if($free) {
                $this->_stmt->free();
                $this->_stmt = null;
            }
            $result = $sth->execute($params)->fetchAll();
        } else {
            $result = $db->queryAll($query);
        }
        if (PEAR::isError($result)) {
            return  $result;
        }
        // ROオブジェクトで返す
        /* @var $ro BEAR_Ro */
        $ro->setBody($result);
        $ro->setHeaders($info);
        $pager->setPagerLinks($links, $info);
        $pager = array('links' => $links, 'info' => $info);
        $ro->setLinks(array('pager' => $pager));
        BEAR::dependency('BEAR_Log')->log('DB Pager', $info);
        return $ro;
        
    }
    
    /**
     * プリペアを毎回freeするセレクト
     *
     * <pre>
     * 通常のselect文の他にDB結果の一部だけをSELECTする機能と、HTMLページングの機能が合わさった
     * メソッドです。getAll()メソッドの引数に加えて一画面に表示するデータ数を
     * 引数に指示するとページング(スライス）されたデータ結果と
     * エージェントに合わせたリンクHTML文字列が返ります。
     * $valuesが配列<array($key => $values)>ならWHERE $key1 = $id1 AND $key2 = $id2 ..と条件を作ってselectします。
     * リソース内で受け取った$valuesを条件にSELETするときに使います。
     *
     * $paramsが空だと通常のSQL、連想配列が入っていると$queryをpreparedステートメート文として期待して実行します。
     * </pre>
     *
     * @param string $query  SQL
     * @param array  $params プリペアードステートメントにする場合にバインドする変数
     * @param array  $values where条件配列
     * @param string $id     
     *
     * @return BEAR_Ro
     */
    public function select($query, array $params = array(), array $values = null, $id = 'id')
    {
        assert(is_object($this->_config['db']));
        assert(is_object($this->_config['ro']));
        $db = &$this->_config['db'];
        $ro = $this->_config['ro'];
        $prepare_free = (isset($this->_config['prepare_free'])) ? $this->_config['prepare_free'] : 1;
        
        // Row取得
        if (!is_null($values)) {
            $result = $this->_selectRow($db, $query, $params, $values, $id, $stmt);
            // prepareステートメントをfree
            $stmt->free();
            if ($result !== false) {
                return $result;
            }
        }
        // All取得
        // 論理削除
        if (isset($this->_config['deleted_at']) && $this->_config['deleted_at'] === true
        ) {
            if (stripos($query, 'WHERE') === false) {
                $query .= ' WHERE deleted_at IS NULL';
            } else {
                $query = str_ireplace('WHERE', 'WHERE deleted_at IS NULL AND', $query);
            }
        }
        // ソート
        if (isset($this->_config['sort'])) {
            $query = $this->_sort($query);
        }
        if ((!isset($this->_config['pager']) || !$this->_config['pager']) || $this->_config['perPage'] <= 0) {
            if (isset($this->_config['offset']) && $this->_config['perPage'] > 0) {
                // LIMIT & Offset
                $query .= ' LIMIT ' . $this->_config['offset'] . ',' . $this->_config['perPage'];
            } elseif (isset($this->_config['perPage']) && $this->_config['perPage'] > 0) {
                // LIMIT
                $query .= ' LIMIT ' . $this->_config['perPage'];
            }
            if ($params) {
                $sth = $db->prepare($query);
                $result = $sth->execute($params)->fetchAll();
                // prepareステートメントをfree
                $sth->free();
            } else {
                $result = $db->queryAll($query);
            }
            return $result;
        }
        // DBページャー
        $pagerOptions = $this->_config['options'];
        $pagerOptions['perPage'] = $this->_config['perPage'];
        if (!array_key_exists('totalItems', $pagerOptions)) {
            $pagerOptions['totalItems'] = $this->_countQuery($query, $params,$count_sth);
            // prepareステートメントをfree
            $count_sth->free();
        }
        // ページング
        $pager = BEAR::dependency('BEAR_Pager');
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
        $info['page_numbers'] = array(
            'current' => $pager->pager->getCurrentPageID(),
            'total' => $pager->pager->numPages()
        );
        list($info['from'], $info['to']) = $pager->pager->getOffsetByPageId();
        $info['limit'] = $info['to'] - $info['from'] + 1;
        $db->setLimit($pagerOptions['perPage'], $info['from'] - 1);
        if ($params) {
            $sth = $db->prepare($query);
            $result = $sth->execute($params)->fetchAll();
            // prepareステートメントをfree
            $sth->free();
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
        $pager->setPagerLinks($links, $info);
        $pager = array('links' => $links, 'info' => $info);
        $ro->setLinks(array('pager' => $pager));
        BEAR::dependency('BEAR_Log')->log('DB Pager', $info);
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
     * @param object &$db
     * @param string $query
     * @param array  $params
     * @param array  $values
     * @param mixed  $id
     * @param object &$sth prepareステートメントオブジェクト
     *
     * @return mixed
     */
    private function _selectRow(&$db, $query, array $params, array $values, $id ,&$sth)
    {
        $where = array();
        if (is_string($id) && array_key_exists($id, $values)) {
            $where[] = $id . ' = ' . self::quote($values[$id], 'text');
        } elseif (is_array($id) && (count(array_intersect($id, array_keys($values))) === count($id))) {
            foreach ($id as &$key) {
                $where[] = $key . ' = ' . self::quote($values[$key], 'text');
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
            if(!is_object($sth)) {
                $sth = $db->prepare($query);
            }
            $result = $sth->execute($params)->fetchRow();
        } else {
            $result = $db->queryRow($query);
        }
        return $result;
    }

    /**
    * カウントクエリー
    *
    * @param string $query
    * @param array  $params
    * @param object &$sth
    *
    * @return int
    */
    protected function _countQuery($query, array $params = array(),&$sth)
    {
        $prepare_free = (isset($this->_config['prepare_free'])) ? $this->_config['prepare_free'] : 1;
        // be smart and try to guess the total number of records
        $countQuery = $this->_rewriteCountQuery($query);
        if ($countQuery) {
            if ($params) {
                $sth = $this->_config['db']->prepare($countQuery);
            } else {
                $totalItems = $this->_config['db']->queryOne($countQuery);
            }
            if (PEAR::isError($totalItems)) {
                return $totalItems;
            }
        } else {
            // GROUP BY => fetch the whole resultset and count the rows returned
            $res = $this->_config['db']->queryCol($query);
            if (PEAR::isError($res)) {
                return $res;
            }
            $totalItems = count($res);
        }
    
        return $totalItems;
    }
    
}
