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
 * Injectorインターフェイス
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
 */
interface BEAR_Injector_Interface
{
    /**
     * Inject
     *
     * 指定オブジェクトに必要なサービスを注入します。
     *
     * Inject対象のオブジェクト
     *
     * @param $object
     * @param $config
     */

    /** @noinspection PhpAbstractStaticMethodInspection */
    public static function inject($object, $config);
}
