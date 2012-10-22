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
 * @version   SVN: Release: $Id: Count.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
/**
 * クエリークラス
 *
 * <pre>
 *
 * @category  BEAR
 * @package   BEAR_Query
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Count.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Mdb2/BEAR_Mdb2.html
 */
class BEAR_Query_Count extends BEAR_Query implements BEAR_Query_Interface
{
    public function select($query, array $params = array())
    {
        $count =  $this->_countQuery($query, $params);
        return $count;
    }
}
