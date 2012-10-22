<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Factory.php 816 2009-08-03 09:24:45Z koriyama@users.sourceforge.jp $
 */

/**
 * BEARファクトリー抽象クラス
 *
 * <pre>
 * Factoryパターンを実現するためのクラスです。
 * このクラスを継承して実装されたクラスはBEAR::dependency()でコールされたときにfactory()メソッドが返した物がインスタンスとなります。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Factory.php 816 2009-08-03 09:24:45Z koriyama@users.sourceforge.jp $
 * @abstract
 *
 */
abstract class BEAR_Factory extends BEAR_Base
{

    public function __construct(array $config)
    {
        parent::__construct($config);
    }
}