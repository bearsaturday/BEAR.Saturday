<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Mock.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * モック用リソースクラス
 *
 * <pre>
 * テスト用モッククラスです。
 * テスト用配列を返します。
 * ['mock']['x'] = array('id', 'name', 'age');
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Mock.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
class BEAR_Resource_Execute_Mock extends BEAR_Resource_Execute_Adaptor
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
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * リソースアクセス
     *
     * リソースを使用します。
     *
     * @param array  $uri     URI(無効)
     * @param array  $values  引数(無効)
     * @param string $method  リクエストメソッド（無効)
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
                $xKey = (isset($this->_config['options']['mock']) && is_array($this->_config['options']['mock']['x'])) ? $this->_config['options']['mock']['x'][$j] : $j;
                $row[$labelX[$j]] = "{$extra}.{$i}.{$xKey}";
            }
            $mock[$i] = $row;
        }
        return $mock;
    }
}