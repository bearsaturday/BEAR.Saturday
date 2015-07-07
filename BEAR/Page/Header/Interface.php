<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Page
 * @subpackage Header
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 */

/**
 * ヘッダーインターフェイス
 *
 * @category   BEAR
 * @package    BEAR_Page
 * @subpackage Header
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 */
interface BEAR_Page_Header_Interface extends BEAR_Base_Interface
{
    /**
     * ヘッダー出力
     *
     * <pre>
     * ヘッダーを出力用にバッファリングします。
     * 引数は配列または文字列で指定できます。
     * スタティック変数として保存されBEAR_Mainで出力バッファを
     * フラッシュする時に送出されます。
     * 同じ<
     * /pre>
     *
     * @param mixed $header HTTPヘッダー
     *
     * @return void
     * @static
     */
    public function setHeader($header);

    /**
     * ヘッダーの取得
     *
     * @return array ヘッダー
     */
    public function getHeaders();

    /**
     * ヘッダーのフラッシュ
     *
     * <pre>
     * ページにヘッダーを取得します。
     * 通常はページ出力時に自動で出力されます。
     * </pre>
     *
     * @return void
     * @static
     */
    public function flushHeader();

    /**
     * リダイレクト
     *
     * <pre>Locationヘッダーを用いてページの移動を行います。
     * クッキーが対応してないエージェントの場合はクエリーに
     * セッションIDを付加します。
     *
     * .(dot)を指定すると同一ページのリフレッシュになります。
     * ページが完全に移動した場合は$config['permanent']をtrueにすると
     * 301ヘッダーを付加してリダイレクトしボットなどに移転を知らせます。
     *
     * -----------------------------------------
     *
     * Example 1. リダイレクト
     * </pre>
     * <code>
     *  $header->redirect('http://www.example.co.jp/');
     * </code>
     * <pre>
     * Example 2. リダイレクト（301 パーマネント)
     * </pre<
     * <code>
     * $header->redirect('/', array('permanent' =>true));
     * </code>
     * <pre>
     * Example 3. 値を渡してリロード
     * </pre
     * <code>
     * // onInit($args)の$argsに渡されます
     * $header->redirect('.', array('args'=$values);
     * </code>
     *
     * <b>$options</b>
     *
     * 'val'       string セッション利用して値を次ページのonInit($args)に変数を渡す値
     * 'click'     string コールするonClickハンドラ
     * 'permanent' bool   301ヘッダー(パーマネントムーブ)を出力するか
     *
     * @param string $url     URL
     * @param array  $options オプション
     *
     * @return void
     */
    public function redirect($url, array $options = array('val' => null, 'click' => null, 'permanent' => false));

    /**
     * リクエストヘッダーの取得
     *
     * @param string $header HTTPヘッダー名
     *
     * @return mixed string | false HTTPヘッダー値、みつからなければfalse
     */
    public function getRequestHeader($header);
}
