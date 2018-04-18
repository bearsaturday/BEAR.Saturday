<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Injectorインターフェイス
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
