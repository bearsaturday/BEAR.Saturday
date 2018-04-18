<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * リソース実行アダプター
 */
abstract class BEAR_Resource_Execute_Adapter extends BEAR_Base
{
    /**
     * リソースリクエスト実行
     *
     * @return mixed
     */
    abstract public function request();
}
