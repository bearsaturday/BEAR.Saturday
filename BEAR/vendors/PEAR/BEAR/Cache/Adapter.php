<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 */
/**
 * キャッシュアダプタークラス
 *
 * <pre>
 * キャッシュ抽象クラスです。BEAR/Cache/Adapter/の各クラスで実装します。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 * @abstract
 *  */
abstract class BEAR_Cache_Adapter extends BEAR_Base
{

    /**
     *
     * キャッシュ時間
     *
     * @var int
     *
     */
    protected $_life = 0;

    /**
     * アダプター
     *
     * @var Cache_Lite | Memcache | APC
     */
    protected $_adapter;

    /**
     * コンストラクタ.
     *
     * @param array $options ユーザーコンフィグ
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
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
     * <pre>
     * キーを基にキャッシュデータを取得します。
     * 無い場合にはデフォルト$defaultsが使われます。
     * </pre>
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
     *
     */
    abstract public function deleteAll();
}