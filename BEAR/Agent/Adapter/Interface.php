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
 * BEAR_Agent_Adapter_Interface
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
 */
interface BEAR_Agent_Adapter_Interface
{
    /**
     * サブミット処理
     *
     * 各UAに応じたサブミット処理（絵文字エンティティや文字コード変換）をします。
     *
     * @return string
     */
    public function submit();
}
