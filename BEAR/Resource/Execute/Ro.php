<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * RO(リソースオブジェクト)リソース
 *
 * ROクラスはメソッドをもちCRUDインターフェイスに対応します。
 */
class BEAR_Resource_Execute_Ro extends BEAR_Resource_Execute_Adapter
{
    /**
     * リソースリクエスト実行
     *
     * App/Ro下のROリソースのリクエストを行います。
     */
    public function request()
    {
        // ROクラスDI
        $config = [
            'method' => $this->_config['method'],
            'uri' => $this->_config['uri'],
            'class' => $this->_config['class']
        ];
        if (isset($this->_config['options']['config'])) {
            $roConfig = array_merge($this->_config, $this->_config['options']['config']);
        } else {
            $roConfig = $this->_config;
        }
        if (isset($this->_config['options']['injector'])) {
            $roOptions = ['injector' => $this->_config['options']['injector']];
        } else {
            // デフォルトインジェクト
            $roOptions = [];
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
            }
            $this->_config['obj']->setBody($result);
        }

        return $this->_config['obj'];
    }
}
