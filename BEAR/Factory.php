<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */

/**
 * ファクトリー
 *
 * Factoryパターンのためのクラスです。
 * このクラスを継承して実装されたクラスはBEAR::dependency()でコールされたときに
 * factory()メソッドが返した物がインスタンスとなります。
 * 利用する側はインスタンスの生成方法を知る事がなくfactoryパターンで
 * インスタンス提供ができます。
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 * @abstract
 */
abstract class BEAR_Factory extends BEAR_Base
{
    /**
     * Factory
     *
     * @return stdClass
     */
    abstract public function factory();
}
