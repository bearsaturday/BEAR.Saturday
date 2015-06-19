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
 * @version    SVN: Release: @package_version@ $Id: Mock.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */

/**
 * リソースモック（スタブ）
 *
 * <pre>
 * テスト用配列を返します。
 * ['mock']['x'] = array('id', 'name', 'age');
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Mock.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Execute_Mock extends BEAR_Resource_Execute_Adapter
{
    /**
     * デフォルト行数
     */
    const X_DEFAULT = 3;

    /**
     * デフォルト行数
     */
    const Y_DEFAULT = 3;

    /**
     * リソースリクエスト実行
     *
     * リソースを使用します。
     *
     * @return mixed
     */
    public function request()
    {
        $mock = array();
        $extra = (isset($this->_config['options']['mock']['name'])) ? $this->_config['options']['mock']['name'] : $this->_config['uri'];
        $y = (isset($this->_config['options']['mock']['y'])) ? $this->_config['options']['mock']['y'] : self::Y_DEFAULT;
        if (isset($this->_config['options']['mock']['x'])) {
            if (is_array($this->_config['options']['mock']['x'])) {
                $labelX = $this->_config['options']['mock']['x'];
                $x = count($this->_config['options']['mock']['x']);
            } else {
                $x = $this->_config['options']['x'];
                $labelX = range(0, $x);
            }
        } else {
            $x = self::X_DEFAULT;
            $labelX = range(0, 4);
        }
        for ($i = 0; $i < $y; $i++) {
            for ($j = 0; $j < $x; $j++) {
                $xKey = (isset($this->_config['options']['mock']) && is_array(
                    $this->_config['options']['mock']['x']
                )) ? $this->_config['options']['mock']['x'][$j] : $j;
                $row[$labelX[$j]] = "{$extra}.{$i}.{$xKey}";
            }
            $mock[$i] = $row;
        }
        return $mock;
    }
}
