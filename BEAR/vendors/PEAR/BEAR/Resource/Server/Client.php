<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Server
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Client.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * BEARデーモンサーバークライアント
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Server
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Client.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
class BEAR_Resource_Server_Client
{

    /**
     * IPアドレス
     *
     * @var string
     */
    private $_ip;

    /**
     * ポート
     *
     * @port int
     */
    private $_port;

    /**
     * 配列でリターンするか
     *
     * @mixed bool
     */
    private $_returnVo = false;

    /**
     *　コンストラクタ
     *
     * @param string $ip       サーバーIP
     * @param int    $port     サーバーポート番号
     * @param bool   $returnVo true BEAR_Ro | false array
     */
    function __construct($ip, $port, $returnVo = false)
    {
        $this->_ip = $ip;
        $this->_port = $port;
        $this->_returnVo = $returnVo;
    }

    /**
     * リソースリクエスト送信
     *
     * @param string $method リクエストメソッド(CRUD)
     * @param string $uri    URI（クエリー付き）
     * @param array  $values 引数
     *
     * @return mixed BEAR_Ro | array
     */
    public function send($method, $uri, array $values = array())
    {
        $socket = new Net_Socket();
        // 接続を確立する
        $socket->connect($this->_ip, $this->_port, true, 30);
        $uriWithVal = self::_mergeQueryAndArray($uri, $values);
        // 改行を含むデータを送信する
        $request = "{$method} {$uriWithVal}";
        $socket->writeLine($request);
        // 改行が現れるまでデータを受信する
        $code = $socket->readLine();
        //　アトリビュート
        $header = $socket->readLine();
        while ($header) {
            $headers[] = $header;
            $header = $socket->readLine();
        }
        $body = $socket->readLine();
        if ($this->_returnVo && class_exists('BEAR_Ro', false)) {
            $ro = BEAR::factory('BEAR_Ro');
            /* @var $ro BEAR_Ro */
            $ro->setBody($body);
            $ro->setHeaders($headers);
            $ro->setCode($code);
            $result = $ro;
        } else {
            $result = array('code' => $code,
                'headers' => $headers,
                'body' => $body);
        }
        return $result;
    }

    /**
     * クエリー付きURIと連想配列のマージ
     *
     * <pre>
     * クエリー部分の文字列を連想配列にしたものと、
     * 連想配列をマージしてクエリー付きのURL文字列としてかえします。
     * </pre>
     *
     * @param string $uri    (クエリー付き)URI
     * @param array  $values 配列
     *
     * @return string
     */
    private function _mergeQueryAndArray($uri, array $values = array())
    {
        $parse = parse_url($uri);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $queryArray);
            if ($values) {
                $values = array_merge($queryArray, $values);
            } else {
                $values = $queryArray;
            }
        }
        $queryStrings = ($values) ? '?' . http_build_query($values) : '';
        $result = $parse['path'] . $queryStrings;
        return $result;
    }
}
