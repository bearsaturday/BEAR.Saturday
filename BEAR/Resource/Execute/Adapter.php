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
 * リソース実行アダプター
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
