<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Ro.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net/
 */

/**
 * RO(リソースオブジェクト)リソース
 *
 * ROクラスはメソッドをもちCRUDインターフェイスに対応します。
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Ro.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Execute_Ro extends BEAR_Resource_Execute_Adapter
{
    /**
     * リソースリクエスト実行
     *
     * App/Ro下のROリソースのリクエストを行います。
     *
     * @return mixed
     */
    public function request()
    {
        // ROクラスDI
        $config = array(
            'method' => $this->_config['method'],
            'uri' => $this->_config['uri'],
            'class' => $this->_config['class']
        );
        if (isset($this->_config['options']['config'])) {
            $roConfig = array_merge($this->_config, $this->_config['options']['config']);
        } else {
            $roConfig = $this->_config;
        }
        if (isset($this->_config['options']['injector'])) {
            $roOptions = array('injector' => $this->_config['options']['injector']);
        } else {
            // デフォルトインジェクト
            $roOptions = array();
        }
        $this->_config['obj'] = BEAR::factory($this->_config['class'], $roConfig, $roOptions);
        // アノテーションクラスDI
        $config['method'] = 'on' . $this->_config['method'];
        $annotation = BEAR::factory('BEAR_Annotation', $config);
        // requireアノテーション (引数のチェック)
        $annotation->required($this->_config['values']);
        // aspectアノテーション (アドバイスの織り込み）
        $method = $annotation->aspect();
        $result = $method->invoke($this->_config['obj'], $this->_config['values']);
        // 後処理
        if (PEAR::isError($result)) {
            $this->_config['obj']->setCode(BEAR::CODE_ERROR);
            $this->_config['obj']->setHeader('_error', $result->toString());
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
