<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * リソースオブジェクト
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
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link      https://github.com/bearsaturday
 *
 * @Singleton
 *
 * @config string method      メソッド
 * @config string uri         URI
 * @config array  values      引き数
 * @config array  options     オプション
 * @config bool   is_ajax_set ajax setリソース？
 * @config array  request     リクエスト設定
 */
class BEAR_Ro extends ArrayObject implements IteratorAggregate, BEAR_Ro_Interface, BEAR_Base_Interface
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
     * コンフィグ
     *
     * @var array
     */
    protected $_config = array();

    /**
     * ボディ
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
     * HTML（文字列）
     *
     * @var string
     */
    private $_html = '';

    /**
     * 状態コード
     *
     * @var int BEAR::CODE_OK | BEAR::CODE_BAD_REQUEST | BEAR::CODE_ERROR
     */
    private $_code = BEAR::CODE_OK;

    /**
     * ページ
     *
     * デフォルトではレジストリのpage(カレントのページ）
     *
     * @var mixed
     */
    private static $_page = 'page';

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $app = BEAR::get('app');
        $class = get_class($this);
        if (isset($app[$class])) {
            $config = array_merge($app[$class], $config);
        }
        $this->_config = (array) $config;
    }

    /**
     * マジックメソッド - 文字列化
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $string = $this->toString();
        } catch (Exception $e) {
            return '';
        }

        return $string;
    }

    /**
     * 関数としての振る舞い
     *
     * @param array $values
     *
     * @return mixed
     */
    public function __invoke(array $values)
    {
        $config = array_merge($this->_config, array('values' => $values));
        $ro = BEAR::factory('BEAR_Resource_Request', $config)->request();

        return $ro;
    }

    /**
     * Inject
     */
    public function onInject()
    {
        $this->_log = BEAR::dependency('BEAR_Log');
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
        $ro = BEAR::factory('BEAR_Ro')->setCode(BEAR::CODE_BAD_REQUEST);

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
        $ro = BEAR::factory('BEAR_Ro')->setCode(BEAR::CODE_BAD_REQUEST);

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
        $ro = BEAR::factory('BEAR_Ro')->setCode(BEAR::CODE_BAD_REQUEST);

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
        $ro = BEAR::factory('BEAR_Ro')->setCode(BEAR::CODE_BAD_REQUEST);

        return $ro;
    }

    /**
     * リンク
     *
     * @param array $values
     *
     * @return array
     */
    public function onLink(
        /* @noinspection PhpUnusedParameterInspection */
        $values
    ) {
        return array();
    }

    /**
     * シンプルアサーション
     *
     * <pre>
     * 単純なboolean値を引数にしてfalseの時は例外を投げ、
     * リソースの結果は400エラー(Bad Request)のリソースオブジェクト(BEAR_Roオブジェクト)になります。
     * onRead, onCreateなどCRUDメソッドに値が正しく渡されているか確認するために使用します。
     * 再利用のためにAOPアドバイスですることも検討してください。
     * </pre>
     *
     * @param bool   $bool 条件
     * @param string $msg  エラー例外のinfo
     *
     * @throws Exception
     */
    public function assert($bool, $msg = 'Bad Resource Request (assert)')
    {
        if (! $bool) {
            throw $this->_exception(
                $msg,
                array(
                    'code' => BEAR::CODE_BAD_REQUEST,
                    'info' => array('request' => (string) $this)
                )
            );
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
     * @throws BEAR_Exception
     *
     * @deprecated @requierdを用います
     */
    public function assertRequired(array $keys, $values)
    {
        if (count(array_intersect($keys, array_keys($values))) != count($keys)) {
            throw $this->_exception(
                'Bad Resource Request (@required)',
                array(
                    'code' => BEAR::CODE_BAD_REQUEST,
                    'info' => array('request' => $this->toString())
                )
            );
        }
    }

    /**
     * リソースボディの取得
     *
     * ボディ（リソース取得結果本体）を取得します。
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->_body;
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
     * リソースヘッダーの取得
     *
     * @param $headerKey
     */
    public function getHeader($headerKey)
    {
        $result = isset($this->_headers[$headerKey]) ? $this->_headers[$headerKey] : null;

        return $result;
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
     * @return BEAR_Ro
     */
    public function setBody($body)
    {
        $this->_body = $body;

        return $this;
    }

    /**
     * リソースHTMLの指定
     *
     * @param $html
     *
     * @return BEAR_Ro
     */
    public function setHtml($html)
    {
        $this->_html = $html;

        return $this;
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
     * @return BEAR_Ro
     */
    public function setHeader($key, $header)
    {
        $this->_headers[$key] = $header;

        return $this;
    }

    /**
     * ヘッダーのセット
     *
     * @param array $headers ヘッダー配列
     *
     * @return BEAR_Ro
     */
    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;

        return $this;
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
     * @return BEAR_Ro
     */
    public function setLink($key, $link)
    {
        $this->_links[$key] = $link;

        return $this;
    }

    /**
     * リンクをセット
     *
     * @param array $links リンク
     *
     * @return BEAR_Ro
     */
    public function setLinks($links)
    {
        $this->_links = $links;

        return $this;
    }

    /**
     * 状態コード設定
     *
     * @param int $code コード
     *
     * @return BEAR_Ro
     */
    public function setCode($code)
    {
        if ($code == BEAR::CODE_OK || $code == BEAR::CODE_BAD_REQUEST || $code == BEAR::CODE_ERROR) {
            $this->_code = $code;
        } else {
            $this->_code = BEAR::CODE_ERROR;
        }

        return $this;
    }

    /**
     * コードの取得
     *
     * @return int (200|400|500)
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * リソースの文字列化
     *
     * <pre>
     * 変数状態のリソース(body)にテンプレートを適用して文字列化します。
     * テンプレート指定がない場合、スカラー値ならそのまま、スカラー値でないなら''になります。
     * プロトタイプリソース（リクエストをまだ行っていないリソース）の場合は実リクエストを行った後に文字列化を行います。
     * このメソッドはRoクラスのマジックメソッドとして機能します。
     * </pre>
     *
     * @return string
     */
    public function toString()
    {
        // リソーステンプレート適用済み？
        if ($this->_html) {
            if ($this->_config['debug'] === true) {
                $this->_html = BEAR::dependency('BEAR_Ro_Debug')->getResourceToString($this);
            }

            return $this->_html;
        }
        // リソースプロトタイプなら実リクエスト
        if ($this instanceof BEAR_Ro_Prototype) {
            if (isset($this->_config['is_ajax_set'])) {
                $html = BEAR::factory('BEAR_Resource_Request_Ajax', $this->_config['request'])->getJs();

                return $html;
            }
            $ro = $this->request();
            $html = $ro->getHtml();
            // リソースデバック
            if ($this->_config['debug'] === true) {
                $html = BEAR::dependency('BEAR_Ro_Debug')->getResourceToString($ro);
            }

            return $html;
        }

        return '';
    }

    /**
     * HTTP出力
     *
     * <pre>
     * BEAR_RoリソースオブジェクトをHTTP出力します。
     * _codeプロパティがレスポンスコード、_header配列プロパティのうち
     * 文字列のものがHTTPヘッダー,_bodyプロパティがHTTPボディとして出力されます。
     * </pre>
     */
    public function outputHttp()
    {
        // ヘッダー
        switch ($this->_code) {
            case BEAR::CODE_BAD_REQUEST:
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                if (! $this->_body) {
                    $this->setBody('400 Bad Request (BEAR)');
                }
                break;
            case BEAR::CODE_ERROR:
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Server Error');
                if (! $this->_body) {
                    $this->setBody('500 Server Error (BEAR)');
                }
                break;
            case BEAR::CODE_OK:
            default:
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
        }
        // this RO headers
        if (is_array($this->_headers)) {
            foreach ($this->_headers as $header) {
                if (is_string($header)) {
                    header($header);
                }
            }
        }

        $log = BEAR::dependency('BEAR_Log');
        $log->log(
            'HTTP Output',
            array(
                'header' => headers_list(),
                'body' => $this->_body
            )
        );
        echo $this->_body;
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
     * $a[] = $value のように呼ばれた場合$offset には null が渡されます
     * </pre>
     *
     * @param mixed $offset セットするオフセット
     * @param mixed $value  セットする値
     *
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
     * @ignore
     */
    public function offsetUnset($offset)
    {
        unset($this->_body[$offset]);
    }

    /**
     * イテレーター
     *
     * optionsで指定したイテレーター、もしくはdefaultのArrayIteratorが使用されます
     *
     * @return IteratorAggregate
     */
    public function getIterator()
    {
        $this->_body = is_array($this->_body) ? $this->_body : array();
        $iterator = (isset($this->_config['options']['iterator']) && class_exists(
            $this->_config['options']['iterator']
        )) ? $this->_config['iterator'] : 'ArrayIterator';
        $obj = new $iterator($this->_body);

        return $obj;
    }

    /**
     * Create a new iterator from an ArrayObject instance
     *
     * @return Traversable
     */
    public function getIterator1()
    {
        $body = is_array($this->body) ? $this->body : array();
        $obj = new $this->iterator($body);

        return $obj;
    }

    /**
     * count取得
     *
     * arrayとして振舞うための実装です。
     *
     * @return int
     */
    public function count()
    {
        return count($this->_body);
    }

    /**
     * array関数の適用
     *
     * array関数適用用のインターフェイスです。
     *
     * @param string $fname 関数名
     * @param mixed  $args  関数の引数
     *
     * @return mixed
     */

    /**
     * ksort
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
     * @param string $key テンプレート変数名 省略すればURI(/を_に置換)
     *
     * @return BEAR_Ro
     */
    public function set($key = null)
    {
        // キー省略
        if (! $key) {
            // 未指定の場合://と/を_に変換してアサイン名に
            $config = $this->getConfig();
            $key = strtolower(str_replace('/', '_', $config['uri']));
        }
        // ページ
        $page = BEAR::dependency('App_Page', self::$_page);
        /* @var $page App_Page */
        $val = (isset($config['ro']) && $config['ro'] === true) ? $this : $this->_body;
        $page->set($key, $val);

        return $this;
    }

    /**
     * Set config
     *
     * @param mixed $config (string) コンフィグキー | (array) コンフィグ配列
     * @param mixed $values (string) $configの時のコンフィグ値
     *
     * @return BEAR_Ro
     */
    public function setConfig($config, $values = null)
    {
        if (is_string($config)) {
            $this->_config[$config] = $values;
        } else {
            $this->_config = $config;
        }

        return $this;
    }

    /**
     * コンフィグ取得
     *
     * @param null $key
     *
     * @return array|mixed
     */
    public function getConfig($key = null)
    {
        if (isset($key)) {
            return $this->_config[$key];
        }

        return $this->_config;
    }

    /**
     * サービスセット
     *
     * @param string $name    プロパティ
     * @param object $service サービス
     */
    public function setService($name, $service)
    {
        $this->$name = $service;
    }

    /**
     * Ro取得
     *
     * link()がついてるリソースでもgetRo()で取得されるのは最初のリソースです。
     *
     * @return App_Ro
     */
    public function getRo()
    {
        return $this;
    }

    /**
     * HTML取得
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->_html;
    }

    /**
     * @deprecated
     * @ignore
     *
     * @return string
     */
    public function getRequestText()
    {
        $result = ("{$this->_config['method']} {$this->_config['uri']}") . ($this->_config['values'] ? '?' . http_build_query(
            $this->_config['values']
        ) : '');

        return $result;
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
        $class = get_class($this) . '_Exception';
        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        if (! file_exists(_BEAR_APP_HOME . "/{$file}") && ! file_exists(_BEAR_BEAR_HOME . "/{$file}")) {
            $class = 'BEAR_Ro_Exception';
        }

        return new $class($msg, $config);
    }
}
