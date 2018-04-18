<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
/**
 * BEAR_Queryインターフェイス
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 */
interface BEAR_Query_Interface
{
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
     * @param array  $params バインドする変数
     *
     * @return BEAR_Ro
     */
    public function select($query, array $params = array());

    /**
     * インサート
     *
     * @param array $values
     * @param null  $table
     * @param null  $types
     *
     * @return mixed|mixeds
     */
    public function insert(array $values, $table = null, $types = null);

    /**
     * アップデート
     *
     * @param array  $values
     * @param string $where
     * @param null   $table
     * @param null   $types
     *
     * @return mixed
     */
    public function update(array $values, $where, $table, $types = null);

    /**
     * デリート
     *
     * @param      $where
     * @param null $table
     *
     * @return mixed
     */
    public function delete($where, $table = null);

    /**
     * クオート
     *
     * @param $value
     * @param $type
     *
     * @return mixed
     */
    public function quote($value, $type);

    /**
     * エラー？
     *
     * @param MDB2_Result $result DB結果
     *
     * @return mixed
     */
    public function isError($result);
}
