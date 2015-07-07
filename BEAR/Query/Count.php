<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Query
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Count.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link      https://github.com/bearsaturday
 */

/**
 * Countクエリー
 *
 * 通常のSELECTの代わりにCOUNTクエリーを行います。
 * onInit()内でBEAR_Queryの代わりにBEAR_Query_Countをインジェクトして使用します。
 *
 * @category  BEAR
 * @package   BEAR_Query
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   Release: @package_version@ $Id: Count.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net
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
