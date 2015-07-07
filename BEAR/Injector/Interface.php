<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Request
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       https://github.com/bearsaturday
 */

/**
 * Injectorインターフェイス
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Request
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
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
     *
     * @return void
     */
    /** @noinspection PhpAbstractStaticMethodInspection */
    public static function inject($object, $config);
}
