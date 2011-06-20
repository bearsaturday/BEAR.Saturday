<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Function.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * 関数リソース
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Function.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Execute_Function extends BEAR_Resource_Execute_Adaptor
{
    /**
     * リソースリクエスト実行
     *
     * 関数をリソースとして扱うクラスです。リクエストメソッドは無視されます。
     *
     * @return mixed
     */
    public function request()
    {
        $result = call_user_func($this->_config['function'], $this->_config['values']);
        return $result;
    }
}