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
 * BEAR Baseインターフェイス
 *
 * <pre>
 * BEARの全クラスのベースとなるクラスのインターフェイスです。
 * </pre>
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
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
