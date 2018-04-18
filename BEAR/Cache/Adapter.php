<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * キャッシュアダプター
 *
 * キャッシュ抽象クラスです。BEAR/Cache/Adapter/の各クラスで実装します。
 */
abstract class BEAR_Cache_Adapter extends BEAR_Base
{
    /**
     * キャッシュ時間
     *
     * @var int
     */
    protected $_life = 0;

    /**
     * アダプター
     *
     * @var BEAR_Cache_Adapter
     */
    protected $_adapter;

    /**
     * @var BEAR_Log
     */
    protected $_log;

    /**
     * Inject
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
