<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Base
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Interface.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $ Interface.php 2455 2011-06-02 02:33:00Z akihito.koriyama@gmail.com $
 * @link       https://github.com/bearsaturday
 */

/**
 * BEAR Baseインターフェイス
 *
 * <pre>
 * BEARの全クラスのベースとなるクラスのインターフェイスです。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Base
 * @subpackage Aspect
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Interface.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $ Interface.php 2455 2011-06-02 02:33:00Z akihito.koriyama@gmail.com $
 * @link       https://github.com/bearsaturday
 */
interface BEAR_Base_Interface
{
    /**
     * Constructor
     *
     * $configが配列なら$_configプロパティとマージされます。
     * 文字列ならそれをファイルパスとして読み込み初期値とします。
     *
     * @param array $config ユーザー設定値
     */
    public function __construct(array $config);

    /**
     * コンフィグセット
     *
     * @param      $config
     * @param null $values
     *
     * @return mixed
     */
    public function setConfig($config, $values = null);

    /**
     * コンフィグ取得
     *
     * @param null $key
     *
     * @return mixed
     */
    public function getConfig($key = null);
}
