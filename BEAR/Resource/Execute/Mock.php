<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * リソースモック（スタブ）
 *
 * <pre>
 * テスト用配列を返します。
 * ['mock']['x'] = array('id', 'name', 'age');
 * </pre>
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
     */
    public function request()
    {
        $mock = [];
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
