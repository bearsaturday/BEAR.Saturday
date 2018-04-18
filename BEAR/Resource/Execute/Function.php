<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
 */

/**
 * 関数リソース
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
 */
class BEAR_Resource_Execute_Function extends BEAR_Resource_Execute_Adapter
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
