<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Output
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: rss.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */

/**
 * RSS/ATOM出力
 *
 * <pre>RSSフィードが格納された連想配列変数$valueをテンプレートにアサインし
 * XML出力します。テンプレートはフレームワークが用意しているRSS2.0/Atom1.0
 * のものがある他、アプリケーションRSSフォルダにアプリケーション定義のXMLファイルを
 * 使用する事もできます。</pre>
 *
 * $value
 * RSS/Atom共通オプション
 *
 * <ul>
 * <li>
 * <var>title</var> <kbd>string</kbd> = <samp>BEAR's blog</samp>
 *      <br />タイトル
 * <var>url</var> <kbd>string</kbd> = <samp>http://www.example.com/</samp>
 *      <br />URL
 * </li><li>
 * <var>description</var> <kbd>string</kbd> = <samp></samp>
 *      <br />RSSの概要
 * </li><li>
 * <var>category</var> <kbd>string</kbd> = <samp>weblog</samp>
 *      <br />URL
 * </li><li>
 * <var>pub_date</var> <kbd>string</kbd> = <samp></samp>
 *      <br />発効日をPHPのtimestampで
 * </li><li>
 * <var>rss_url</var> <kbd>string</kbd> = <samp>http://www.example.com/rss/</samp>
 *      <br />URL
 * </li><li>
 * <var>generator</var> <kbd>integer</kbd> = <samp></samp>
 *      <br />サービス名
 * </li><li>
 * <var>entry</var> <kbd>array</kbd> = <samp></samp>
 *      <br />エントリー、アイテム
 * </li><li>
 *
 * $value['item']
 * アイテム、エントリー
 *
 * <var>title</var> <kbd>string</kbd> = <samp></samp>
 *      <br />タイトル
 * </li><li>
 * <var>link</var> <kbd>string</kbd> = <samp></samp>
 *      <br />リンク
 * </li><li>
 * <var>description</var> <kbd>string</kbd> = <samp></samp>
 *      <br />本文
 * </li><li>
 * <var>category</var> <kbd>string</kbd> = <samp></samp>
 *      <br />カテゴリー
 * </li><li>
 * <var>entries</var> <kbd>string</kbd> = <samp></samp>
 *      <br />コメントURL（省略化）
 * </li><li>
 * <var>author</var> <kbd>string</kbd> = <samp></samp>
 *      <br />エントリー、アイテム（省略化）
 * </li><li>
 * <var>pub_date</var> <kbd>string</kbd> = <samp></samp>
 *      <br />エントリー、アイテム
 * </li><li>
 *
 *
 * $option
 * RSS/Atom共通オプション
 * </ul>
 * </li>
 * <var>header</var> <kbd>mixed</kbd> = <samp>Content-Type: application/xml</samp>
 *      <br />RSS/ATOM出力時のヘッダーです。stringで単数、arrayで渡すと複数のヘッダーが
 *      <br />出力されます。指定しないとContent-Type: application/xmlが出力されます。
 *      <br />application/xml+rdfとしていないのは現時点での互換性のためです。
 *
 * -----------------------------------------
 * Example 1. Atom1.0形式で配信
 *
 * $option['rss_type'] = 'atom1_0';
 * $this->dataOutput('RSS', $values, $option);
 *
 * @param array $values  値
 * @param array $options オプション
 *
 * @return void
 * @see BEAR/BEAR_Data::getRss
 *
 * @ignore
 * @throws BEAR_Resource_Exception
 */
function outputRss(
    /** @noinspection PhpUnusedParameterInspection */
    $values,
    /** @noinspection PhpUnusedParameterInspection */
    array $options
) {
    throw new BEAR_Resource_Exception('not implement yet');
}