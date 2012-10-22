<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Iterator.php 835 2009-08-18 03:54:51Z koriyama@users.sourceforge.jp $
 */

/**
 * ROイテレータークラス
 *
 * <pre>
 * ROが配列として扱われた場合の振る舞いが記述してあります。
 * BEAR_RoでIteratorAggregateインターフェイスの実装に使っています。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Iterator.php 835 2009-08-18 03:54:51Z koriyama@users.sourceforge.jp $
 *
 */
class BEAR_Ro_Iterator implements Iterator
{

    private $_arr;

    public function __construct(array $arr)
    {
        $this->_arr = $arr;
    }

    public function update($arr)
    {
        $this->_arr = $arr;
    }

    public function rewind()
    {
        reset($this->_arr);
    }

    public function key()
    {
        return key($this->_arr);
    }

    public function &current()
    {
        return $this->_arr[key($this->_arr)];
    }

    public function next()
    {
        next($this->_arr);
    }

    public function valid()
    {
        return !is_null(key($this->_arr));
    }
}