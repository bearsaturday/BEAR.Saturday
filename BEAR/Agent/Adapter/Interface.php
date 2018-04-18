<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * BEAR_Agent_Adapter_Interface
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
