<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: BEAR.php 1322 2010-01-05 08:26:47Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR/BEAR.html
 */

/**
 * フレームワークホームパス
 *
 */
define('_BEAR_BEAR_HOME', realpath(dirname(__FILE__)));

/**
 * 現在時刻 (W3CDTFフォーマット）
 *
 */
define('_BEAR_DATETIME', date('c', $_SERVER['REQUEST_TIME']));

/**
 * BEARシステムクラス
 *
 * フレームワーク全体で必要なプロパティ、スタティックメソッドを持ちます。
 *
 * Example. （メソッド）配列読み込み
 *
 * ini, XML, YAMLファイルなどスタティックなファイルを配列として読み込みます。
 * 結果は時間無制限でキャッシュされます。PHP配列も受け付けそのまま出力します。
 *
 * </pre>
 *
 * <code>
 * $target = 'config.xml';
 * $values = BEAR::loadValues($target);
 * </code>
 *
 * @category BEAR
 * @package  BEAR
 * @author   Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @license  http://opensource.org/licenses/bsd-license.php BSD
 * @version  SVN: Release: $Id: BEAR.php 1322 2010-01-05 08:26:47Z koriyama@users.sourceforge.jp $
 * @link     http://api.bear-project.net/BEAR/BEAR.html
 *
 * <pre>
 * Copyright (c) 2008, Akihito Koriyama.  All rights reserved.
 *
 * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * </pre>
 */
class BEAR
{

    /**
     * BEARバージョン
     */
    const VERSION = '0.8.13';

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
     * サービス
     *
     * サービスロケーターのサービスとして扱います。
     */
    private static $_registry = array();

    /**
     * デフォルトインジェクト
     *
     * @var array
     */
    private static $_defaultInjectors = array();

    /**
     * 設定
     */
    private static $_config = array();

    /**
     * 初期化
     *
     * <pre>
     * フレームワークの初期化を行います。フレームワーク内部でのみ使用されます。
     * 機能は以下の通り。
     *
     *  デバック用関数読み込み
     *  シャットダウン関数登録
     *  アプリケーション設定をappとしてレジストリ保存
     * </pre>
     *
     * @return void
     */
    public static function init(array $appConfig)
    {
    	$done = false;
        if ($done === true) {
            trigger_error('you call twice for BEAR::init');
            return true;
        }
        $done = true;
        // クラスローダー登録

        spl_autoload_register(array('BEAR', 'onAutoload'));
        BEAR::set('app', new ArrayObject($appConfig));
        if ($appConfig == array()){
            return;
        }
        self::$_config = $appConfig['core'];
        if (self::$_config['debug']) {
            include 'BEAR/BEAR/debug.php';
            PEAR::registerShutdownFunc(array('BEAR',
                'shutdownFunctionDebug'));
        } else {
            include _BEAR_BEAR_HOME . '/BEAR/BEAR/live.php';
        }
        // シャットダウン関数登録
        if (PHP_SAPI === 'cli' && defined('_BEAR_APP_HOME')) {
            ini_set('include_path', get_include_path() . ':' . _BEAR_APP_HOME);
        }
        // エラー初期化
        $validPath = array(_BEAR_APP_HOME . '/htdocs', _BEAR_APP_HOME . '/App');
        // BEAR developperのみBEAR内のエラー表示
        if (isset($_SERVER['beardev']) && $_SERVER['beardev']) {
            $validPath[] = _BEAR_BEAR_HOME;
        }
        $pandaConfig = array(Panda::CONFIG_DEBUG => $appConfig['core']['debug'],  // デバックモード
        Panda::CONFIG_VALID_PATH => $validPath,  // エラーレポートするファイルパス
        Panda::CONFIG_LOG_PATH => _BEAR_APP_HOME . '/logs/', // fatalエラーログを保存するパス
        Panda::CONFIG_CATCH_STRICT => true,                // strict エラー
        Panda::CONFIG_ON_IS_CLI_OUTPUT => array('BEAR_Main', 'isCliErrorOutput'));
        Panda::init($pandaConfig);
    }

    /**
     * クラスローダー
     *
     * <pre>
     * 定義されていないクラスが使用されたときにコールされP（るクラスローダーです。
     * PEAR, BEAR, Appクラスそれぞれに対応しています。
     * _を/に変更したパスがクラスパスとして使用されます。
     * 使用しないようにするには以下のようにします
     * </pre>
     *
     * @param string $class クラス名
     *
     * @return void
     */

    public static function onAutoload($class)
    {
        if (class_exists($class, false)) {
            return;
        }
        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        @include $file;
        // クラス宣言を含むかどうか確認する
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }
        // auto loader error
        if (!class_exists('BEAR_Exception', false)) {
            include _BEAR_BEAR_HOME. '/BEAR/Exception.php';
        }
        $info = array('class' => $class, 'file' => $file);
        throw new BEAR_Exception('BEAR Auto loader failed.', compact('info'));
    }

    /**
     * デバック時のシャットダウン処理
     *
     * @return void
     *
     * @ignore
     */
    public static function shutdownFunctionDebug()
    {
        if (class_exists('BEAR_Log')) {
            BEAR_Log::onShutDownDebug();
        }
    }

    /**
     * 連想配列の取得
     *
     * PHPの連想配列を指定すればそのまま、ファイルパスを指定すれば
     * 設定ファイルから読み込み連想配列として渡します。
     * またURLのクエリー形式も使用できます。
     * BEARで広く使われています。BEARの全てのクラスのコンストラクタ
     * （シングルトン含む）、リソースへの引数、
     * オプションにこの連想配列フォーマットが使われます。
     *
     * 通常ファイルフォーマットは拡張子で指定されますが、
     * $options['extention']でオーバーロードできます。
     *
     * array --　連想配列としてオプションが入力されます
     *
     * string -- ファイルから拡張子によりフォーマットが異なります
     *  URLクエリー形式　?foo1=bar1&hoge=fugaのようなフォーマットを連想配列にします
     *  *.ini iniフォーマット
     *  *.xml XMLフォーマット
     *  *.php phpのdefineが連想配列として読み込まれます
     *  *.yml yamlファイル
     *
     * <b>$options</b>
     * 'extention' string オーバーロード拡張子
     * </pre>
     *
     * @param mixed $target  ターゲット　ファイルパス,URLなど
     * @param array $options オプション
     *
     * @return array
     * @see http://pear.php.net/manual/ja/package.configuration.config.php
     * @see BEAR/test/files/example.ini
     * @see BEAR/test/files/example.xml
     * @see BEAR/test/files/example.php
     *
     */
    public static function loadValues($target, $options = array())
    {
        if (!is_file((string)$target)) {
            // arrayならそのまま
            if (is_array($target) || is_object($target)) {
                return (array)$target;
            }
            // false | null なら 設定な
            if (!$target) {
                return null;
            }
            // クエリーがあるときはクエリーをパースした連想配列を返す
            $parseUrl = parse_url($target);
            if (isset($parseUrl['query'])) {
                $options = array();
                parse_str($parseUrl['query'], $options);
                return $options;
            } else {
                return null;
            }
        } else {
            $cache = BEAR::dependency('BEAR_Cache');
            $cache->setLife(BEAR_Cache::LIFE_UNLIMITED);
            $key = $target . filemtime($target);
            $cacheResult = $cache->get($key);
            if ($cacheResult) {
                return $cacheResult;
            }
            // PEAR::Configを使って設定ファイルをパース
            $pathinfo = pathinfo($target);
            // 相対パスなら絶対パスに (/:linux :win)
            $target = (substr($target, 0, 1) == '/' || substr($target, 1, 1) == ':') ? $target : _BEAR_APP_HOME . '/App/Ro/' . $target;
            $extension = isset($options['extention']) ? $options['extention'] : $pathinfo['extension'];
            switch ($extension) {
                case 'yml' :
                    if (false && function_exists('syck_load')) {
                        $content = file_get_contents($target);
                        $yaml = syck_load($content);
                    } else {
                        include_once 'BEAR/inc/spyc-0.2.5/spyc.php';
                        $yaml = Spyc::YAMLLoad($target);
                    }
                    $cache->set($key, $yaml);
                    return $yaml;
                case 'csv' :
                    $conf = File_CSV::discoverFormat($target);
                    $csv = array();
                    while ($fields = File_CSV::read($target, $conf)) {
                        array_push($csv, $fields);
                    }
                    $result = $cache->set($key, $csv);
                    return $csv;
                case 'ini' :
                    $parse = 'inicommented';
                    break;
                case 'xml' :
                    $parse = 'xml';
                    break;
                case 'php' :
                    $parse = 'PHPConstants';
                    break;
                default :
                    return file_get_contents($target, FILE_TEXT);
                    break;
            }
            $config = new Config();
            $root = &$config->parseConfig($target, $parse);
            if (PEAR::isError($root)) {
                $msg = '設定を読み込む際のエラー: ';
                $msg .=$root->getMessage();
                $info = array('parse' => $parse,
                    'input' => $target);
                throw new BEAR_Exception($msg, array(
                    'info' => $info));
                return false;
            } else {
                $result = $root->toArray();
                return $result['root'];
            }
        }
    }

    /**
     * コンフィグファイル読み込み
     *
     * @param string $target YAMLコンフィグファイルパス
     *
     * @return array
     *
     * @todo var_exportのキャッシュ化
     */
    public static function loadConfig($target)
    {
        // syck_loadが不安定
        if (false && function_exists('syck_load')) {
            $content = file_get_contents($target);
            $yaml = syck_load($content);
        } else {
            include_once 'BEAR/inc/spyc-0.2.5/spyc.php';
            $yaml = Spyc::YAMLLoad($target);
        }
        return $yaml;
    }

    /**
     * BEARファクトリー
     *
     * <pre>
     * 汎用ファクトリーメソッドです。生成したオブジェクトにfactoryという名前のメソッドが
     * 含まれるとそのメソッドが生成したオブジェクトを返します。
     * </pre>
     *
     * @param string $class  クラス名
     * @param array  $config クラス用コンフィグ
     *
     * @return object 生成されたオブジェクト
     *
     */
    public static function factory($class, array $config = array(), array $options = array())
    {
        // app.yml
        static $app = null;

        assert(is_string($class));
        // default
        if (is_null($app)) {
            $app = BEAR::get('app');
        }
        // default;
        if ($config === array() && isset(self::$_registry['app'][$class])) {
            $config = self::$_registry['app'][$class];
        }
        if (isset(self::$_registry['app']['core'])){
            $core = self::$_registry['app']['core'];
            assert(is_array($core));
            $config = array_merge($config, $core);
        }
        try {
        	self::onAutoload($class);
        } catch (Exception $e){
        	try {
                // App_* -> BEAR_* (for backward compatibility)
                $bearClass = str_replace('App', 'BEAR', $class);
                self::onAutoload($bearClass);
                $class = $bearClass;
            } catch (Exception $e){
                $info = compact('class');
            	throw new BEAR_Exception('Auto loader failed',  array('code' => BEAR::CODE_BAD_REQUEST, 'info' => $info));
            }
        }
        if (isset($options['agent']) && $options['agent'] == 'method') {

        }
        $object = new $class($config);
        // inject
        if (isset(self::$_defaultInjectors[$class])) {
            $injector = self::$_defaultInjector[$class];
        } else {
            $injector = isset($options['injector']) ? $options['injector'] : 'onInject';
        }
        if (is_string($injector)) {
        	if (method_exists($object, $injector)){
                $object->$injector();
        	}
        } elseif (is_array($injector)){
        	if (is_callable(array($injector[0], $injector[1]))) {
                call_user_func(array($injector[0], $injector[1]), $object, $config);
        	} else {
                throw new BEAR_Exception('Injector is not valid.', array(
                'code' => BEAR::CODE_BAD_REQUEST,
                'info' => compact('injector')));
        	}
        }
        // factoryオブジェクトなら生成したインスタンスを返す
        if ($object instanceof BEAR_factory) {
            assert(method_exists($object, 'factory'));
            $object = $object->factory();
        }
        return $object;
    }

    /**
     * デフォルトインジェクトを指定
     *
     * @param $class
     * @param $injector
     *
     * @return void
     */
    public function setDefaultInjector($class, $injector)
    {
        self::$_defaultInjector[$class] = $injector;
    }

    /**
     * デフォルトインジェクトを解除
     *
     * @param $class
     *
     * @return void
     */
    public function unsetDefaultInjector($class)
    {
        unset(self::$_defaultInjector[$class]);
    }

    /**
     * DIコンテナとサービスロケーター
     *
     * <pre>
     * DIコンテナとサービスロケーターを兼用するメソッドです。
     * オブジェクトをシングルトンで生成してレジストリーに登録します。
     *
     * $configの変数の型によって動作が変わります。
     *
     * 配列の場合 :
     *  $configが配列だとそのBEAR::factoryでその配列をコンストラクタに渡しオブジェクトを生成します。
     *  生成したオブジェクトはサービスプロパティにオブジェクトを格納します。
     *
     * 文字列の場合 :
     *  サービスロケーターの呼び出しとして機能します。与えられた文字列をキーにレジストリからオブジェクトを返します。
     *  レジストリにオブジェジェクトの代わりに配列($class, $config)でが入っているとその情報でインスタンス化します。
     *  これはlazy-load(遅延ロード）を実現するものです。実際の読み込みまでインスタンス生成コストがかかりません。
     *
     * オブジェクトの場合 :
     *  そのオブジェクトをそのまま返します。
     * </pre>
     *
     * @param string $class    依存オブジェクトクラス名
     * @param mixed  $config   array=クラスコンフィグ string=サービスキー　object=そのまま返る
     * @param mixed  $options  (bool) シングルトンで生成するか (array) オプション
     *
     * @return object
     *
     * @see http://java.sun.com/blueprints/corej2eepatterns/Patterns/ServiceLocator.html Core J2EE Patterns - Service Locator
     */
    public static function dependency($class, $config = array(), $options = true)
    {
    	// 代入
        if (is_object($config)) {
            return $config;
        }
        // シングルトン
        if (isset(self::$_registry[$class])) {
            return self::$_registry[$class];
        }
        // persistent
        if (isset($options['persistent']) && $options['persistent'] !== false){
            $cache = self::dependency('BEAR_Cache');
            $object = $cache->get($class);
            if (is_object($object)) {
                return $object;
            }
        }
        // サービスロケーター
        if (is_string($config)) {
            if (!isset(self::$_registry[$config])) {
                $msg = "Service is not exists";
                $info = array('service' => $config);
                throw new BEAR_Exception($msg, array(
                    'info' => $info));
            }
            $service = self::$_registry[$config];
            if (is_object($service)) {
                return self::$_registry[$config];
            } elseif (is_array($service)) {
                // 遅延ロード
                self::$_registry[$service[0]] = BEAR::factory($service[0], $service[1]);
                return self::$_registry[$service[0]];
            } else {
                $msg = "No service in BEAR::dependency";
                $info = array('class' => $class);
                throw new BEAR_Exception($msg, array(
                    'info' => $info));
            }
        }
        if (is_array($options)) {
            $options = array_merge(array('is_singleton' => true,
                'inject' => 'onInject'), $options);
        } else {
            $options = array('is_singleton' => $options);
        }
        //オブジェクト作成
        if ($options['is_singleton']) {
        	// シングルトン
            $object = isset(BEAR::$_registry[$class]) ? BEAR::$_registry[$class] : false;
            if ($object !== false) {
                if ($object instanceof BEAR_Base) {
                    $object->setConfig($config);
                }
            } else {
            	BEAR::$_registry[$class] = $object = BEAR::factory($class, $config, $options);
            	if (isset($options['persistent']) && $options['persistent'] !== false){
                    $cache->set($class, $object);
                }
            }
        } else {
            $object = BEAR::factory($class, $config, $options);
        }
        return $object;
    }

    /**
     * オブジェクトをレジストリにセット
     *
     * <pre>
     * サービスをレジストリにセットします。
     * lazy-load（遅延ロード）でオブジェトをセットしたい場合は
     * array($class, $config)というフォーマットで$serviceを配列にします。
     * 読み込み時に$configを引数にインスタンス化されます。
     * </pre>
     *
     * <code>
     * // オブジェクトのセット
     * BEAR::set('App_Foo', $obj);
     * // lazy-loadでのオブジェクトのセット
     * // BEAR::dependencyで使用されるときにnewされます。
     * BEAR::set('App_Foo', array('App_Foo', $config));
     *
     * @param string $key     サービスオブジェクトキー
     * @param mixed  $service サービス object | array($class, $config) lazy-load
     *
     * @return void
     */
    public static function set($key, $service)
    {
        if (self::exists($key)) {
            $msg = 'Service Already Exists （BEAR::dependencyでキーがすでに登録されています)';
            $info = array('key' => $key);
            throw new BEAR_Exception($msg, array('info' => $info));
        }
        self::$_registry[$key] = $service;
    }

    /**
     * サービスを取得
     *
     * サービスネームからサービスを取得します。
     *
     * @param string $key サービスキー
     *
     * @return object
     *
     * @throw ErrorException
     */
    public static function get($key)
    {
        assert(is_string($key));
        if (!isset(self::$_registry[$key])) {
            $msg = "Service is not Exists";
            $info = array('service key' => $key);
            throw new BEAR_Exception($msg, array('info' => $info));
        }
        if (is_array(self::$_registry[$key])) {
            self::$_registry[$key] = BEAR::factory(self::$_registry[$key][0], self::$_registry[$key][1]);
        }
        return self::$_registry[$key];
    }

    /**
     * レジストリにサービスがあるか調べる
     *
     * @param string $key サービスキー
     *
     * @return bool
     */
    public static function exists($key)
    {
        return (isset(self::$_registry[$key]));
    }

    /**
     * レジストリ内容を全て取得
     *
     * @return array
     */
    public static function getAll()
    {
        return self::$_registry;
    }
}
