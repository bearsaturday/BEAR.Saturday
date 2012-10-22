<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Resource.php 1228 2009-11-17 12:40:35Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * BEAR_Resourceクラス
 *
 * <pre>
 * リソースを操作するリソースブラウザクラスです。
 *
 * 操作の動作をオプションで指定します。キャッシュ、ページング処理、
 * コールバック関数によるポストプロセス、作成時の二重動作禁止のための
 * トークン処理などの機能があります。リソースファイル（リソースをアクセス
 * するファイル）は単純関数とクラス形式の２つがあります。
 * PROJECT_ROOT/App/resource以下にフォルダの階層も持って配置されます。
 *
 * <pre>
 * Example. キャッシュの使用
 *
 * </pre>
 * <code>
 * $options['cache']['key'] = 'cacheid_foo'; //省略できます
 * $options['cache']['life'] = 30;
 * $blog = $resoruce->read($params, $options);
 * </code>
 *
 * <pre>
 * Example. 二重実行防止にはPOE(Post Once Exactly)オプションを指定します
 *
 * <code>
 * //二重送信されたものを一度しか実行しない
 * $options['poe'] = true;
 * $resource->create($values, $options);
 * </code>
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Resource.php 1228 2009-11-17 12:40:35Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
class BEAR_Resource extends BEAR_Base implements BEAR_Resource_Request_Interface
{

    /**
     * リソースメソッド - create(POST, INSERT)
     */
    const METHOD_CREATE = 'create';

    /**
     * リソースメソッド - read(GET, SELECT)
     */
    const METHOD_READ = 'read';

    /**
     * リソースメソッド - update(PUT, UPDATE)
     */
    const METHOD_UPDATE = 'update';

    /**
     * リソースメソッド - delete(DELETE, DELETE)
     */
    const METHOD_DELETE = 'delete';

    /**
     * リソースオプション ページャー
     */
    const OPTION_PAGER = 'pager';

    /**
     *　リソースキャッシュ　キャッシュキー
     */
    const OPTION_CACHE_KEY = 'key';

    /**
     * リソースオプションキー　キャッシュ時間
     */
    const OPTION_CACHE_LIFE = 'life';

    /**
     * リソースオプションキー スタティックリソースキー
     *
     */
    const OPTION_RESOURCE_FILE_EXTENTION = 'extention';

    /**
     * オプション　POE (Post Once Exactly)
     */
    const OPTION_POE = 'poe';

    /**
     * オプション　トークン無視
     */
    const OPTION_TOKEN = 'token';

    /**
     * リンクキー pager
     *
     */
    const LINK_PAGER = 'pager';

    /**
     * リソースオブジェクト
     *
     * @var BEAR_Ro
     */
    private $_ro;

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * ページャー情報の取得
     *
     * <pre>ページャーで生成されたナビゲーションHTMLを取得します。
     * DBページャーの場合はページャー情報も取得されます
     * ['links']にナビゲーションリンクが
     * ['info']のページャー情報がはいります
     *
     * DBページャーの場合は結果がリソースオブジェクトになっているので、
     * リンクとメタ情報からページャー情報を生成しています</pre>
     *
     * @return array
     */
    public function getPager()
    {
        $links = $this->_ro->getLinks();
        $result = array('links' => $links['pager'], 'info' => $this->_ro->getHeaders());
        return $result;
    }

    /**
     * リソース作成
     *
     * <pre>
     * リソースを作成します。
     *
     * $params
     * 'uri'     string URI
     * 'values'  array  引数
     * 'options' array  オプション
     *
     * </pre>
     *
     * @param array $params リクエストパラメータ
     *
     * @return BEAR_Ro
     */
    public function create(array $params)
    {
        $values = isset($params['values']) ? $params['values'] : array();
        $options = isset($params['options']) ? $params['options'] : array();
        $this->_ro = $this->request(self::METHOD_CREATE, $params['uri'], $values, $options);
        return $this->_ro;
    }

    /**
     * リソース読み込み
     *
     * <pre>
     * リソースを読み込みます。
     *
     * $params
     * 'uri'     string URI
     * 'values'  array  引数
     * 'options' array  オプション
     *
     * $params['options']
     *  'cache' 'id'   string キャッシュID
     *  'cache' 'life' int    キャッシュ時間（秒）
     *
     * @param array $params 引数
     *
     * @return BEAR_Ro
     */
    public function read(array $params)
    {
        $values = isset($params['values']) ? $params['values'] : array();
        $options = isset($params['options']) ? $params['options'] : array();
        $this->_ro = $this->request(self::METHOD_READ, $params['uri'], $values, $options);
        return $this->_ro;
    }

    /**
     * リソース更新
     *
     * リソースを更新します。
     *
     * $options
     *
     *  'poe'            bool   二重実行防止
     * </pre>
     *
     * @param array $params 引数
     *
     * @return BEAR_Ro
     */
    public function update(array $params)
    {
        $values = isset($params['values']) ? $params['values'] : array();
        $options = isset($params['options']) ? $params['options'] : array();
        $this->_ro = $this->request(self::METHOD_UPDATE, $params['uri'], $values, $options);
        return $this->_ro;
    }

    /**
     * リソース削除
     *
     * リソースを削除します。
     *
     * $params
     * 'uri'     string URI
     * 'values'  array  引数
     * 'options' array  オプション
     * </pre>
     *
     * @param array $params 引数
     *
     * @return BEAR_Ro
     */
    public function delete(array $params)
    {
        $values = isset($params['values']) ? $params['values'] : array();
        $options = isset($params['options']) ? $params['options'] : array();
        $this->_ro = $this->request(self::METHOD_DELETE, $params['uri'], $values, $options);
        return $this->_ro;
    }

    /**
     * リソース操作
     *
     * create(), read(), update(), delete()から呼ばれるリソース操作クラスです。
     *
     * 外部リソース（DB、ファイル等）に対しての操作を行います。
     * リソース名に対応するリソースファイルがApp/resouce/フォルダから
     * 読み込まれ$valuesで受け取った値を渡します。
     * 帰り値はgetResult()メソッドで取得できます。
     *
     * <pre>
     * DB、ファイルなどの外部リソースからデータを取得します。リソースファイルを
     * App/resourceフォルダに用意したものが使われます。
     * 引数から外部リソースを操作した値を連想配列で返します。
     *
     * キャッシュオプションを指定することでキャッシュが使用できます。キーは自動で
     * 生成されるのでキャッシュ時間を指定するだけの透過的な使用ができます。
     * リモートリソースの場合もキャッシュは有効です。
     *
     * ビューの作成に失敗した場合はPEARのエラーを返すとキャッシュが生成されません。
     *
     * $options
     *
     *  'callback'       string コールバック関数名
     *  'poe'            bool   二重実行防止
     *
     * <pre>
     * Example 1.リソース読み込み
     * </pre>
     * <code>
     * $params['uri'] = 'user/profile';
     * $params['values'] = array('id'=>1);
     * $resource = BEAR::dependency('BEAR_Resource');
     * $topic = $resource->read($params);
     * </code>
     *
     * @param string $values  引数
     * @param mixed  $options オプション
     *
     * @return  BEAR_Ro
     */
    public function request($method, $uri, array $values = array(), array $options = array())
    {
        assert(is_string($method));
        assert(is_string($uri));
        // URIのクエリーと$valuesをmerge
        $parse = parse_url($uri);
        if (!isset($parse['scheme'])) {
            $this->_mergeQuery($uri, $values);
        }
        $this->_options = $options;
        // トークン
        if (isset($options['notoken']) && !$options['notoken']) {
            if ($this->_isTokenValid($options) === false) {
                return false;
            }
        }
        $config = compact('method', 'uri', 'values', 'options');
        // リクエストクラスを注入 返り値はエラーに関わらずBEAR_Roコンテナ
        $this->resoueceRequest = BEAR::factory('BEAR_Resource_Request', $config);
        $this->_ro = $this->resoueceRequest->request();
        $this->_log->resourceLog($method, $uri, $values, $this->_ro->getCode());
        return $this->_ro;
    }

    /**
     * URIについたクエリーをmergeする
     *
     * <pre>
     * usr?id=1というURIはuriがuserでvaluesが　array('id'=>1)として扱われます。
     * </pre>
     *
     * @param array $values 引数
     *
     * @return void
     */
    private function _mergeQuery(&$uri, array &$values = array())
    {
        $newUri = $newValues = null;
        $parse = parse_url($uri);
        $query = isset($parse['query']) ? $parse['query'] : '';
        parse_str($query, $parsedValues);
        if ((bool)$parsedValues === false) {
            return;
        }
        // ?の前を_uriにock
        $uri = $parse['path'];
        // URIにクエリー引数がついていて場合にはmergeする
        $values = array_merge($values, $parsedValues);
    }

    /**
     * リソースオブジェクトの取得
     *
     * <pre>
     * BEAR_Resource内で生成されたリソースの結果であるバリューオジェクトを返します。
     *　</code>
     *
     * @return void
     */
    public function getRo()
    {
        return $this->_ro;
    }
}