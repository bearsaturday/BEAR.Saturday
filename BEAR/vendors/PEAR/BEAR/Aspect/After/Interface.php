<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Aspect
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 780 2009-07-28 23:49:34Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Aspect/BEAR_Aspect.html
 */
/**
 * afterアドバイスインターフェイス
 *
 * <pre>
 * afterアドバイスを実装するメソッドが使用するインターフェイスです。
 * afterアドバイスはジョインポイントのメソッドが実行された後に織り込まれるアドバイスです。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Aspect
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 780 2009-07-28 23:49:34Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Aspect.html
 */
interface BEAR_Aspect_After_Interface
{

    /**
     * afterアドバイス
     *
     * @param array $values バリュー
     *
     * @return void
     */
    public function after(array $values);
}