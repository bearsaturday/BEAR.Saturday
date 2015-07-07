<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Factory.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net/
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
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Factory.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net/
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
