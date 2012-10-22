<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Ro.php 1327 2010-01-19 03:26:38Z yoshitaka.jingu@excite.jp $
 * @link      http://api.bear-project.net/BEAR_Ro/BEAR_Ro.html
 */
/**
 * リソースオブジェクトクラス
 *
 * <pre>
 * リソースのリソースオブジェクトクラスです。
 * リソースの内容とリソースに対するCRUDインターフェイスを備えています。
 * ボディ情報（リソース状態）、メタ情報（リソースヘッダー）、リソースリンク、
 * 状態コードのプロパティを持ち、リソースの作成、読み込み、変更、削除の
 * インターフェイスを持つ事ができます。
 *
 * BEAR_Resouceクラスがリソースを扱うクラスなのに対して、
 * リソースとして扱われるがこのクラスのオブジェクトです。
 * CSVファイルなどのスタティックリソースや、外部RSS等のリモートリソースも
 * このリソースオブジェクトに変換されて扱われます。
 *
 * このオブジェクトはHTTP通信の仕組みそのものにも似ています。
 * ヘッダーとボディを持ち、POST/GET/PUT/DELETEに対応する
 * インターフェイス(onCreate, onRead, onUpdate, onDelete)を持ちます。
 * このオブジェクトそのものをHTTP出力することができます。x
 * ml/json/phpなどのフォーマットを指定して
 * outputHttp()メソッドを使うとそのままHTTP出力されます。
 *
 * Example. オブジェクト格納例）DBページャーの結果RO
 *
 * ボディ　：　ページングされたデータ
 * ヘッダー：　件数や現在の表示ページ番号などのメタ情報
 * リンク　：　次ページリンクHTML
 * コード　 : 200(OK)
 *
 * 以下は指定したフォーマットで時間を取得できるmyTimeリソースの実装例です。
 * </pre>
 * <code>
 * class myTime extends BEAR_Ro
 * {
 *   public function onRead($values){
 *      $format = $values['format'];
 *      retruen date($format);
 *   }
 * }
 * </code>
 * <pre>
 * ※　時間は削除、変更、作成はできないのでreadメソッドだけが実装されています。
 * ※　returnのフォーマットは2種類あります。
 * RAWデータ（配列やスカラー値）かリソースオブジェクトです。
 *
 * データだけを返せば十分なときはRAWデータでデータにヘッダーやリンクなどの
 * 属性情報をつけて返したい場合はリソースオブジェクト(BEAR_ROオブジェクト）
 * で返します。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Ro.php 1327 2010-01-19 03:26:38Z yoshitaka.jingu@excite.jp $
 * @link      http://api.bear-project.net/BEAR_Ro/BEAR_Ro.html
 */
class BEAR_Ro extends ArrayObject implements BEAR_Ro_Interface, IteratorAggregate, BEAR_Base_Interface
{

    /**
     * コード OK
     */
    const CODE_OK = 200;

    /**
     * コード Bad Request
     */
    const CODE_BAD_REQUEST = 400;

    /**
     * コード　Internal Error
     */
    const CODE_ERROR = 500;

    /**
     * set() configキー
     */
    const CONFIG_PAGER = 'pager';

    /**
     * 最初のリンクフラグ
     *
     * @bool
     */
    private static $_isFirstLink = true;

    /**
     * リンク連結ボディ
     *
     * @var array
     */
    private static $_linksBody = array();

    /**
     * バリュー
     *
     * @var mixed
     */
    private $_body;

    /**
     * メタ情報
     *
     * @var array
     */
    private $_headers;

    /**
     * リンク情報
     *
     * @var array
     */
    private $_links;

    /**
     * 状態コード
     *
     * @var int BEAR::CODE_OK | BEAR::CODE_BAD_REQUEST | BEAR::CODE_ERROR
     */
    private $_code = BEAR::CODE_OK;

    /**
     * リソース結果値
     *
     * @var array
     */
    private static $_values = array();

    /**
     * ページ
     *
     * デフォルトではレジストリのpage(カレントのページ）
     *
     * @var mixed
     */
    private static $_page = 'page';

    /**
     * コンフィグ
     *
     * @var array
     */
    protected $_config = array();

    /**
     * コンストラクタ
     *
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * リソース作成
     *
     * リソースを作成します。このメソッドはキャッシュオプションが使えます。
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function onCreate($values)
    {
        $ro = BEAR::factory('BEAR_Ro');
        $ro->setCode(BEAR::CODE_BAD_REQUEST);
        $ro->setHeader('values', $values);
        return $ro;
    }

    /**
     * リソース読み込み
     *
     * オプションにcacheが使えます
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function onRead($values)
    {
        $ro = BEAR::factory('BEAR_Ro');
        $ro->setCode(BEAR::CODE_BAD_REQUEST);
        $ro->setHeader('values', $values);
        return $ro;
    }

    /**
     * リソース変更
     *
     * リソースを変更します。このメソッドはPOEオプション
     * （一度だけ実行する）オプションが使えます。
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function onUpdate($values)
    {
        $ro = BEAR::factory('BEAR_Ro');
        $ro->setCode(BEAR::CODE_BAD_REQUEST);
        $ro->setHeader('values', $values);
        return $ro;
    }

    /**
     * リソース消去
     *
     * リソースを消去します。このメソッドはPOEオプション
     * （一度だけ実行する）オプションが使えます。
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function onDelete($values)
    {
        $ro = BEAR::factory('BEAR_Ro');
        $ro->setCode(BEAR::CODE_BAD_REQUEST);
        $ro->setHeader('values', $values);
        return $ro;
    }

    /**
     * シンプルアサーション
     *
     * <pre>
     * 単純なboolean値を引数にしてfalseの時は例外を投げ、
     * リソースの結果は400エラー(Bad Request)のリソースオブジェクト(BEAR_Roオブジェクト)になります。
     * onRead, onCreateなどCRUDメソッドに値が正しく渡されているか確認するために使用します。
     * </pre>
     *
     * @param bool $bool 条件
     *
     * @return void
     */
    public static function assert($bool)
    {
        if (!$bool) {
            throw $this->_exception('Bad Resource Request', array(
                'code' => BEAR::CODE_BAD_REQUEST));
        }
    }

    /**
     * 必須項目アサーション
     *
     * <pre>
     * 連想配列に指定のキー配列が全て含まれてるか検査し、問題があれば例外を投げます。
     * リソースの結果は400エラー(Bad Request)のリソースオブジェクト(BEAR_Roオブジェクト)になります。
     * onRead, onCreateなどCRUDメソッドに値が正しく渡されているか確認するために使用します。
     * </pre>
     *
     * @param array $keys   必須キー配列
     * @param array $values テストする配列
     *
     * @return void
     * @throw BEAR_Exception
     */
    public function assertRequired(array $keys, $values)
    {
        if (count(array_intersect($keys, array_keys($values))) != count($keys)) {
            throw $this->_exception('Bad Resource Request', array(
                'code' => BEAR::CODE_BAD_REQUEST));
        }
    }

    /**
     * リソースボディの取得
     *
     * ボディ（リソース取得結果本体）を取得します。
     *
     * @return string
     */
    public function getBody($allLink = false)
    {
        if ($allLink === true) {
        	$result = BEAR_Ro::$_linksBody;
        	$this->_killLink();
        } else {
        	$result = $this->_body;
        }
        return $result;
    }

    /**
     * リソースヘッダーの取得
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * リソースリンクの取得
     *
     * リソースのリンクを取得します。
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * リソースボディをセット
     *
     * リソースのボディ（リソース結果）をセットします。
     *
     * @param mixed $body ボディ
     *
     * @return void
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }

    /**
     * リソースヘッダーセット
     *
     * <pre>
     * キーを指定してリソースヘッダーをセットします。
     * 予約済みキーはこのクラスのconstとして定義されています。
     * </pre>
     *
     * @param string $key    ヘッダーキー
     * @param string $header ヘッダー
     *
     * @return void
     */
    public function setHeader($key, $header)
    {
        $this->_headers[$key] = $header;
    }

    /**
     * ヘッダーのセット
     *
     * @param array $headers ヘッダー配列
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;
    }

    /**
     * リンクのセット
     *
     * <pre>
     * リソースリンクをセットします。
     * 予約済みキーはこのクラスのconstとして定義されています。
     * </pre>
     *
     * @param string $key  リンクキー
     * @param string $link リンク
     *
     * @return void
     */
    public function setLink($key, $link)
    {
        $this->_links[$key] = $link;
    }

    public function setLinks($links)
    {
         $this->_links = $links;
    }

    /**
     * 状態コード設定
     *
     * @param int $code コード
     *
     * @return void
     *
     */
    public function setCode($code)
    {
        if ($code == BEAR::CODE_OK || $code == BEAR::CODE_BAD_REQUEST || $code == BEAR::CODE_ERROR) {
            $this->_code = $code;
        } else {
            $this->_code = BEAR::CODE_ERROR;
        }
    }

    /**
     * 状態コードの取得
     *
     * @return int code
     *
     * @return int (200|400|500)
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * 文字列化
     *
     * シリアリズしてbodyを返します。
     *
     * @return string
     */
    public function __toString()
    {
        return serialize($this->_body);
    }

    /**
     * HTTP出力
     *
     * <pre>
     * BEAR_RoリソースオブジェクトをHTTP出力します。
     * _codeプロパティがレスポンスコード、_header配列プロパティのうち
     * 文字列のものがHTTPヘッダー,_bodyプロパティがHTTPボディとして出力されます。
     * </pre>
     *
     * @return void
     */
    public function outputHttp()
    {
        //ヘッダー
        switch ($this->_code) {
        case BEAR::CODE_BAD_REQUEST :
            header('HTTP/1.x 400 Bad Request');
            if (!$this->_body) {
                $this->setBody('400 Bad Request (BEAR)');
            }
            break;
        case BEAR::CODE_ERROR :
            header('HTTP/1.x 500 Server Error');
            if (!$this->_body) {
                $this->setBody('500 Server Error (BEAR)');
            }
            break;
        case BEAR::CODE_OK :
        default :
            header('HTTP/1.x 200 OK');
        }
        if (is_array($this->_headers)) {
            foreach ($this->_headers as $header) {
                if (is_string($header)) {
                    header($header);
                }
            }
        }
        $log = BEAR::dependency('BEAR_Log');
        $log->log('HTTP Output', array('header' => headers_list(),
            'body' => $this->_body));
        echo $this->_body;
        exit();
    }

    /**
     * ArrayObject配列の個別ROWオフセット取得
     *
     * <pre>
     * 暗黙的に offsetExists が呼ばれたりはしない。
     * & を使ったリファレンス返しはできない。
     * </pre>
     *
     * @param mixed $offset オフセット
     *
     * @return mixed
     *
     * @ignore
     */
    public function offsetGet($offset)
    {
        return $this->_body[$offset];
    }

    /**
     * 値をbodyにセット
     *
     * <pre>
     * & を使ったリファレンス渡しはできません。
     * $a[] = $value のように呼ばれた場合$offset には null が渡されます。
     * </pre>
     *
     * @param mixed $offset セットするオフセット
     * @param mixed $value  セットする値
     *
     * @return void
     * @ignore
     */
    public function offsetSet($offset, $value)
    {
        $this->_body[$offset] = $value;
    }

    /**
     * issetで呼ばれbodyに値があるか調べる。
     *
     * <pre>
     * array_key_exists では呼ばれません。
     * </pre>
     *
     * @param mixed $offset オフセット
     *
     * @return bool
     * @ignore
     */
    public function offsetExists($offset)
    {
        return isset($this->_body[$offset]);
    }

    /**
     *　unsetで呼ばれ指定のオフセットのbody値を消去。
     *
     * <pre>
     * array_key_exists では呼ばれません。
     * </pre>
     *
     * @param mixed $offset オフセット
     *
     * @return void
     * @ignore
     */
    public function offsetUnset($offset)
    {
        unset($this->_body[$offset]);
    }

    /**
     * イテレーター取得
     *
     * arrayとして振舞うための実装です。
     *
     * @return BEAR_Ro_Iterator
     * @ignore
     */
    public function getIterator()
    {
        $this->_body = is_array($this->_body) ? $this->_body : array();
        $obj = BEAR::factory('BEAR_Ro_Iterator', $this->_body);
        return $obj;
    }

    /**
     * count取得
     *
     * arrayとして振舞うための実装です。
     *
     * @return int
     * @ignore
     */
    public function count()
    {
        return count($this->_body);
    }

    /**
     * array関数の適用
     *
     * <pre>
     * array関数適用用のインターフェイスです。
     * </pre>
     *
     * @param string $fname 関数名
     * @param mixed  $args  関数の引数
     *
     * @return mixed
     *
     * @ignore
     */
    //    public function __call($fname, $args)
    //    {
    //        if (function_exists("array_$fname")) {
    //            $arr = call_user_func_array("array_$fname", array(
    //                (array)$this->_body) + $args);
    //            if (is_array($arr)) {
    //                $this->_body = $arr;
    //                return $this;
    //            } else {
    //                return $arr;
    //            }
    //        }
    //    }
    /**
     * ksour
     *
     * @return BEAR_Ro
     * @ignore
     */
    public function ksort()
    {
        parent::ksort();
        return $this;
    }

    /**
     * append
     *
     * @param mixed $val 追加する値
     *
     * @return BEAR_Ro
     * @ignore
     */
    public function append($val)
    {
        parent::append($val);
        return $this;
    }

    /**
     * ビューにセット
     *
     * readの後ろにつなげて使います。
     *
     * <pre>
     * $config
     * 'pager' string ページャーをアサインする変数名
     * 'ro'    bool   ROアサイン
     * </pre>
     *
     * <code>
     * $resource->$read($params)->set('user');
     * </code>
     *
     * @param string $tplVar テンプレート変数名 省略すればURI(/を_に置換)
     * @param array  $config 設定
     *
     * @return void
     */
    public function set($tplVar = false, array $config = array())
    {
        $page = BEAR::dependency('App_Page', self::$_page);
        assert($page instanceof BEAR_Page === true);
        // link()が実行済みの場合
        if (BEAR_Ro::$_linksBody) {
            $this->_setLinksBody($page);
            $this->_setPager($page);
            return $this;
        }
        if ($tplVar === false) {
            // 未指定の場合://と/を_に変換してアサイン名に
            $tplVar = strtolower(str_replace('/', '_', $this->_headers['request']['uri']));
        }
        /* @var $page App_Page */
        $val = (isset($config['ro']) && $config['ro'] === true) ? $this : $this->_body;
        $page->set($tplVar, $val);
        $this->_setPager($page);
        return $this;
    }

    /**
     * linkの結果をアサイン
     *
     * @return void
     */
    private function _setLinksBody(&$page)
    {
        foreach (BEAR_Ro::$_linksBody as $tplVar => $val) {
            $page->set($tplVar, $val);
        }
    }

    /**
     * ページャーセット
     *
     * ページャーがあればアサインします。デフォルトはpagerです。
     *
     * @return void
     */
    private function _setPager(&$page)
    {
        if (isset($this->_links['pager'])) {
            $pager = array('links' => $this->_links['pager'],
                'info' => $this->_headers);
            $tplVar = isset($config['pager']) ? $config['pager'] : self::CONFIG_PAGER;
            /* @var $page App_Page */
            $page->set($tplVar, $pager);
        }
    }

    /**
     * デバック表示
     *
     * <code>
     * $resource->$read($params)->p();
     * $resource->$read($params)->set('user')->p();
     * </code>
     *
     * @return BEAR_Ro
     */
    public function p($outputMode = 'printa')
    {
        $trace = debug_backtrace();
        $place = isset($trace[1]['function']) ? " in {$trace[1]['class']}" : '';
        $resource = BEAR::dependency('BEAR_Resource');
        $ro = $resource->getRo();
        $options['label'] = get_class($ro) . '->link(' . implode(',',array_keys(BEAR_Ro::$_linksBody)) . ')' . $place;
        if (BEAR_Ro::$_linksBody) {
            p(BEAR_Ro::$_linksBody, $outputMode, $options);
        } else {
            p($this->_body, $outputMode, $options);
        }
        return $this;
    }

    /**
     * リソースリンクを取得
     *
     * <pre>
     * リソースのリンクを取得します。
     * リンクはリンクキーをキーにリンクURIを値にした配列をROリソースの中のonLinkメソッドで返す事で実現できます。
     * </pre>
     */
    public function link($link)
    {
        //readをset
        if (BEAR_Ro::$_isFirstLink === true) {
            $tplVar = strtolower(str_replace(DIRECTORY_SEPARATOR, '_', $this->_headers['request']['uri']));
            BEAR_Ro::$_linksBody[$tplVar] = $this->_body;
            BEAR_Ro::$_isFirstLink = false;
        }
        $resource = BEAR::dependency('BEAR_Resource');
        $ro = $resource->getRo();
        if (!($ro instanceof BEAR_Ro)) {
            $config = array(
                'info' => array(
                    'ro class' => get_class($ro)));
            throw $this->_exception('Target Resource is not valid.', $config);
        }
        $body = $this->getBody();
        if (!method_exists($ro, 'onLink')) {
            $info = array('resource class' => get_class($ro));
            throw $this->_exception('onLink method is not implemented.', array(
                'code' => BEAR::CODE_BAD_REQUEST,
                'info' => $info));
        }
        $onLinks = $ro->onLink($body);
        $links = is_array($link) ? $link : array($link);
        foreach ($links as $link) {
            // no link err
            if (!isset($onLinks[$link])) {
                $info = array('link uri' => $link,
                    'available links' => $onLinks);
                throw $this->_exception('Resource link key is not exist.', array(
                    'code' => BEAR::CODE_BAD_REQUEST,
                    'info' => $info));
            }
            if(is_array($onLinks[$link])) {
                $emptyParams = array('uri' => '', 'values' => array(), 'options' => array());
                $params = array_merge($emptyParams, $onLinks[$link]);
            } else {
                $params = array('uri' => $onLinks[$link], 'values' => array(), 'options' => array());
            }
            $body = $resource->read($params)->getBody();
            // linkをセット
            self::$_linksBody[$link] = $body;
            }
        return $this;
    }

    /**
     * リンクをキル
     *
     * @return void
     */
    private function _killLink()
    {
    	self::$_isFirstLink = true;
    	self::$_linksBody = array();
    }
    /**
     * コンフィグセット
     *
     * @return void
     */
    public function setConfig(array $config)
    {
        $this->_config = $config;
    }

    /**
     * コンフィグ取得
     *
     * @return void
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * 例外の作成
     *
     * @param string $msg
     * @param array  $config
     *
     * @return Exception
     */
    protected function _exception($msg, array $config)
    {
        try {
            $class = get_class($this) . '_Exception';
        } catch (Exception $e) {
            $class = 'BEAR_Ro_Exception';
        }
        return new $class($msg, $config);
    }

    /**
     * @deperecated
     * @ignore
     */
    public static function getValues()
    {
        return self::$_values;
    }

    /**
     * @deperecated
     * @ignore
     */
    public static function setPage(&$page = 'page')
    {
        self::$_page = &$page;
    }

    /**
     * Ro取得
     *
     * @return App_Ro
     */
    public function getRo()
    {
        return $this;
    }
}
