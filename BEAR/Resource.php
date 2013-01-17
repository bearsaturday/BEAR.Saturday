<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Resource.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * リソースクライアント
 *
 * <pre>
 * リソースの’メソッド’、'URI', 引数(Values)、およびオプションを指定してリソースを操作します。
 * オプションはキャッシュ、ページング処理、コールバック関数によるポストプロセス、作成時の二重動作禁止のための
 * トークン処理などがあります。
 *
 * Example. キャッシュの使用
 * </pre>
 * <code>
 * $options['cache']['key'] = 'cacheid_foo'; //省略できます
 * $options['cache']['life'] = 30;
 * $resoruce->read($params, $options)->set('user', 'object');
 * //または
 * $user = $resoruce->read($params, $options)->getBody();
 * </code>
 *
 * <pre>
 * Example. 二重実行防止にはPOE(Post Once Exactly)オプションを指定します
 * </pre>
 * <code>
 * //二重送信されたものを一度しか実行しない
 * $options['poe'] = true;
 * $resource->create($values, $options)->request();
 * </code>
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Resource.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 *
 * @Singleton
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
     * オプション　CSRF (Cross Site Request Forgeries)
     */
    const OPTION_CSRF = 'csrf';

    /**
     * オプション　トークン無視
     */
    const OPTION_TOKEN = 'token';

    /**
     * リンクキー page
     *
     */
    const LINK_PAGER = 'pager';

    /**
     * リソースオブジェクト
     *
     * @var BEAR_Ro_Prototype
     */
    private $_ro;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $app = BEAR::get('app');
        $this->_config['path'] = $app['BEAR_View']['path'];
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
     * @return BEAR_Ro_Prototype
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
     *  </pre>
     *
     * @param array $params 引数
     *
     * @return BEAR_Ro_Prototype
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
     *  </pre>
     *
     * @param array $params 引数
     *
     * @return BEAR_Ro_Prototype
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
     * @return BEAR_Ro_Prototype
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
     * リソースの操作情報を保持するオブジェクト（BEAR_Ro_Prototype)を取得します。
     * 同クラスのcreate(), read(), update(), delete()から呼ばれるリソース操作クラスです。
     *
     * <pre>
     * Example 1.リソース読み込み
     * </pre>
     * <code>
     * $params['uri'] = 'user/profile';
     * $params['values'] = array('id'=>1);
     * $resource = BEAR::dependency('BEAR_Resource');
     * $profileObject = $resource->read($params)->getRo();
     * $profileValues = $resource->read($params)->getBody();
     * // objectとしてテンプレートにset
     * $profileObject = $resource->read($params)->set('profile', 'object');
     * </code>
     *
     * @param string $method  メソッド(create | read | update | delete)
     * @param string $uri     URI
     * @param array  $values  引数
     * @param array  $options オプション
     *
     * @return BEAR_Ro_Prototype
     */
    public function request($method, $uri, array $values = array(), array $options = array())
    {
        $config = compact('method', 'uri', 'values', 'options');
        $ro = BEAR::factory('BEAR_Ro_Prototype', array('request' => $config, 'path' => $this->_config['path']));
        return $ro;
    }
}