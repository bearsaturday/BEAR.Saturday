<?php
/**
 * BEAR
 *
 * PHP versions 5
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
