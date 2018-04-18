<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * BEAR_Roインターフェイス
 */
interface BEAR_Ro_Interface
{
    /**
     * リソース作成
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function onCreate($values);

    /**
     * リソース読み込み
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function onRead($values);

    /**
     * リソース変更
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function onUpdate($values);

    /**
     * リソース削除
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function onDelete($values);

    /**
     * リソースボディの取得
     *
     * @return mixed
     */
    public function getBody();

    /**
     * リソースヘッダーの取得
     *
     * @return array
     */
    public function getHeaders();

    /**
     * リソースリンクの取得
     *
     * @return array
     */
    public function getLinks();

    /**
     * リソースボディをセット
     *
     * リソースのボディ（リソース結果）をセットします。
     *
     * @param mixed $body ボディー
     */
    public function setBody($body);

    /**
     * リソースヘッダーセット
     *
     * <pre>
     * キーを指定してリソースヘッダーをセットします。
     * 予約済みキーはこのクラスのconstとして
     * 定義されています。
     * </pre>
     *
     * @param array $key    キー
     * @param array $header ヘッダー
     */
    public function setHeader($key, $header);

    /**
     * リンクのセット
     *
     * @param array $key  キー
     * @param array $link リンク
     */
    public function setLink($key, $link);

    /**
     * 状態コード設定
     *
     * @param int $code コード
     */
    public function setCode($code);

    /**
     * 状態コードの取得
     *
     * @return int
     */
    public function getCode();

    /**
     * HTTP出力
     */
    public function outputHttp();
}
