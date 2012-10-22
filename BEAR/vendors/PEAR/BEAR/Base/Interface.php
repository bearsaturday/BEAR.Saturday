<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Base
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Base/BEAR_Base.html
 */
/**
 * BEAR Baseインターフェイス
 *
 * <pre>
 * BEAR_Baseの全クラスのベースとなるクラスです。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Base
 * @subpackage Aspect
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Interface.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Base.html
 */
interface BEAR_Base_Interface
{

    /**
     * コンストラクタ.
     *
     * $configが配列なら$_configプロパティとマージされます。
     * 文字列ならそれをファイルパスとして読み込み初期値とします。
     *
     * @param mixed $config ユーザー設定値
     *
     * @return void
     *
     */
    public function __construct(array $config);

    /**
     * コンフィグセット
     *
     * @return void
     */
    public function setConfig(array $config);

    /**
     * コンフィグ取得
     *
     * @return void
     */
    public function getConfig();
}