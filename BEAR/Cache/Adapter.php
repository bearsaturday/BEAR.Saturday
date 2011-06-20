<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Cache
 * @subpackage Adapter
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Adapter.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * キャッシュアダプター
 *
 * キャッシュ抽象クラスです。BEAR/Cache/Adapter/の各クラスで実装します。
 *
 * @category   BEAR
 * @package    BEAR_Cache
 * @subpackage Adapter
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Adapter.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 */
abstract class BEAR_Cache_Adapter extends BEAR_Base
{
    /**
     * キャッシュ時間
     *
     * @var int
     *
     */
    protected $_life = 0;

    /**
     * アダプター
     *
     * @var BEAR_Cache_Adaptor
     */
    protected $_adapter;

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * キャッシュ保存
     *
     * キャッシュにデータを保存します。(なければ新規作成、あれば更新)
     *
     * @param string $key   キャッシュキー
     * @param mixed  $value 値
     *
     * @return bool
     */
    abstract public function set($key, $value);

    /**
     * キャッシュを取得
     *
     * キーを基にキャッシュデータを取得します。
     * 無い場合にはデフォルト$defaultsが使われます。
     *
     * @param string $key     キャッシュキー
     * @param mixed  $default デフォルト値
     *
     * @return mixed
     */
    abstract public function get($key, $default = null);

    /**
     * キャッシュの削除
     *
     * @param string $key キャッシュキー
     *
     * @return bool
     */
    abstract public function delete($key);

    /**
     * キャッシュの全削除
     *
     * @return bool
     */
    abstract public function deleteAll();
}