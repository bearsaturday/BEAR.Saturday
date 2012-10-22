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
 * @version   SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
/**
 * BEAR_Queryインターフェイス
 *
 * @category  BEAR
 * @package   BEAR_Query
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
interface BEAR_Query_Interface
{
    public function __construct(array $config);

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
     * @return mixeds
     */
    public function insert(array $values, $table = null);

    /**
     * アップデート
     *
     * @return mixed
     */
    public function update(array $values, $where, $table = null);

    /**
     * デリート
     *
     * @return mixed
     */
    public function delete($where, $table = null);

    /**
     * クオート
     *
     * @return strings
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