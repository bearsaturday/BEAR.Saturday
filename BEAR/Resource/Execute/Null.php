<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
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
