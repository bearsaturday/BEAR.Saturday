<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 */

/**
 * Countクエリー
 *
 * 通常のSELECTの代わりにCOUNTクエリーを行います。
 * onInit()内でBEAR_Queryの代わりにBEAR_Query_Countをインジェクトして使用します。
 *
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 *
 * @Singleton
 */
class BEAR_Query_Count extends BEAR_Query implements BEAR_Query_Interface
{
    /**
     * セレクト
     *
     * @param string $query  クエリー
     * @param array  $params パラメータ
     *
     * @return int カウント数
     */
    public function select($query, array $params = array())
    {
        $count = $this->_countQuery($query, $params);

        return $count;
    }
}
