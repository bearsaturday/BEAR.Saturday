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
 * @version    SVN: Release: $Id: Ro.php 1260 2009-12-08 14:41:23Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * ROリソースクラス
 *
 * <pre>
 * ROクラスをリソースとして扱うクラスです。ROクラスはメソッドをもちCRUDインターフェイスに対応します。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Ro.php 1260 2009-12-08 14:41:23Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 *  */
class BEAR_Resource_Execute_Ro extends BEAR_Resource_Execute_Adaptor
{

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
     * @param void
     *
     * @return mixed
     */
    public function request()
    {
        // ROクラスDI
        $config = array(
            'method' => $this->_config['method'],
            'uri' => $this->_config['uri'],
            'class' => $this->_config['class']);
        if (isset($this->_config['options']['config'])) {
            $roConfig = array_merge($this->_config, $this->_config['options']['config']);
        } else {
            $roConfig = $this->_config;
        }
        if (isset($this->_config['options']['injector'])) {
            // インジェクト変更
            $roOptions = array('injector'=>$this->_config['options']['injector']);
        } else {
            // デフォルトインジェクト
            $roOptions = array();
        }
        $this->_config['obj'] = BEAR::factory($this->_config['class'], $roConfig, $roOptions);
        // アノテーションクラスDI
        $config['method'] = 'on' . $this->_config['method'];
        $annotation = BEAR::factory('BEAR_Annotation', $config);
        //        // requireアノテーション (引数のチェック)
        $annotation->required($this->_config['values']);
        //        // aspectアノテーション (アドバイスの織り込み）
        $method = $annotation->aspect();
        $result = $method->invoke($this->_config['obj'], $this->_config['values']);
        // 後処理
        if (PEAR::isError($result)) {
            $this->_config['obj']->setCode(BEAR::CODE_ERROR);
        } else {
            if ($result instanceof BEAR_Ro) {
                // return RO
                return $result;
            } else {
                $this->_config['obj']->setBody($result);
            }
        }
        return $this->_config['obj'];
    }
}
