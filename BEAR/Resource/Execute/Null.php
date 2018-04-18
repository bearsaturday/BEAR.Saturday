<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * Nullリソース
 */
class BEAR_Resource_Execute_Null extends BEAR_Resource_Execute_Adapter
{
    /**
     * リソースリクエスト実行
     *
     * @return mixed
     */
    public function request()
    {
        return null;
    }
}
