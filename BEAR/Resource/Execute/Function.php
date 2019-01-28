<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * 関数リソース
 */
class BEAR_Resource_Execute_Function extends BEAR_Resource_Execute_Adapter
{
    /**
     * リソースリクエスト実行
     *
     * 関数をリソースとして扱うクラスです。リクエストメソッドは無視されます。
     */
    public function request()
    {
        return call_user_func($this->_config['function'], $this->_config['values']);
    }
}
