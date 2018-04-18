<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
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
