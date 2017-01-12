<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */

/**
 * BEAR_Roインターフェイス
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
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
     *
     * @return void
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
     *
     * @return void
     */
    public function setHeader($key, $header);

    /**
     * リンクのセット
     *
     * @param array $key  キー
     * @param array $link リンク
     *
     * @return void
     */
    public function setLink($key, $link);

    /**
     * 状態コード設定
     *
     * @param int $code コード
     *
     * @return void
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
     *
     * @return void
     */
    public function outputHttp();
}
