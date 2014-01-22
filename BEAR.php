<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: BEAR.php 2580 2011-06-20 09:16:25Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

// フレームワークホームパス
define('_BEAR_BEAR_HOME', realpath(dirname(__FILE__)));

// 現在時刻 (W3CDTFフォーマット）
define('_BEAR_DATETIME', date('c', $_SERVER['REQUEST_TIME']));

/**
 * BEARシステムクラス
 *
 * 初期化、クラスの生成に関するスタティックメソッドを持ちます。
 *
 * @category BEAR
 * @package  BEAR
 * @author   Akihito Koriyama <koriyama@bear-project.net>
 * @license  http://opensource.org/licenses/bsd-license.php BSD
 * @version  SVN: Release: @package_version@ $Id: BEAR.php 2580 2011-06-20 09:16:25Z koriyama@bear-project.net $
 * @link     http://www.bear-project.net/
 *
 * <pre>
 * Copyright (c) 2008-2011, Akihito Koriyama.  All rights reserved.
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
     * BEAR version
     */
    const VERSION = '0.9.19';

    /**
     * Code OK
     */
    const CODE_OK = 200;

    /**
     * Code Bad Request
     */
    const CODE_BAD_REQUEST = 400;

    /**
     * COde　Internal Error
     */
    const CODE_ERROR = 500;

    /**
     * グローバルレジストリ
     *
     * @var array
     */
    private static $_registry = array();

    /**
     * 設定
     */
    private static $_config = array();

    /**
     * Constructor - static only
     */
    final private function __construct()
    {
    }

    /**
     * 初期化
     *
     * BEARの使用時にbootstrapなどの部分で一度呼び使用クラスの設定
     * オートローダー、エラーハンドラの設定、デバック用機能有効化などを行います。
     *
     * @param array $appConfig アプリケーション別クラス設定
     *
     * @return void
     */
    public static function init(array $appConfig = array('core' => array('debug' => false)))
    {
        static $_run = false;

        if ($_run === true) {
            return;
        }
        $_run = true;
        // PEAR_Errorがオートローダー効かないための事前require
        /** @noinspection PhpIncludeInspection */
        include_once 'PEAR.php';
        // クラスオートローダー登録
        self::set('app', new ArrayObject($appConfig));
        self::$_config = $appConfig['core'];
        if (self::$_config['debug'] === false) {
            spl_autoload_register(array(__CLASS__, 'onAutoload'));
            //Panda (live)
            $pandaConfig = isset($appConfig['Panda']) ? $appConfig['Panda'] : array();
            $pandaConfig[Panda::CONFIG_DEBUG] = false; // デバックモードオフ
            if (defined('_BEAR_APP_HOME')) {
                $pandaConfig[Panda::CONFIG_LOG_PATH] = _BEAR_APP_HOME . '/logs/';
            }
            Panda::init($pandaConfig);
        } else {
            if (isset($appConfig['BEAR']['autoload'])) {
                spl_autoload_register($appConfig['BEAR']['autoload']);
            } else {
                spl_autoload_register(array(__CLASS__, 'onAutoload'));
            }

            include _BEAR_BEAR_HOME . '/BEAR/BEAR/script/debug_init.php';
        }
        if (PHP_SAPI === 'cli' && defined('_BEAR_APP_HOME')) {
            ini_set('include_path', _BEAR_APP_HOME . PATH_SEPARATOR . get_include_path());
        }
    }

    /**
     * クラスローダー
     *
     * 定義されていないクラスが使用されたときにコールされるクラスローダーで
     * PEARのコーディングスタイルに従ったファイル配置のファイルが自動で読み込まれます。
     *
     * @param string $class クラス名
     *
     * @return void
     *
     * @see http://groups.google.com/group/php-standards/web/psr-0-final-proposal
     * @throws BEAR_Exception
     */
    public static function onAutoload($class)
    {
        if (class_exists($class, false)) {
            return;
        }
        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        /** @noinspection PhpIncludeInspection */
        include_once $file;
        // クラス宣言を含むかどうか確認する
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }
        // auto loader error
        if (!class_exists('BEAR_Exception', false)) {
            include _BEAR_BEAR_HOME . '/BEAR/Exception.php';
        }
        $info = array('class' => $class, 'file' => $file);
        throw new BEAR_Exception("BEAR Auto loader failed for [$class]", compact('info'));
    }

    /**
     * 連想配列の取得
     *
     * <pre>
     * PHPの連想配列を指定すればそのまま、ファイルパスを指定すれば
     * 設定ファイルから読み込み連想配列として渡します。
     * またURLのクエリー形式も使用できます。
     * BEARで広く使われています。BEARの全てのクラスのコンストラクタ
     * （シングルトン含む）、リソースへの引数、
     * オプションにこの連想配列フォーマットが使われます。
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
     * $options
     * 'extention' string オーバーロード拡張子
     * </pre>
     *
     * @param mixed $target  ターゲット　ファイルパス,URLなど
     * @param array $options オプション
     *
     * @return array
     *
     * @see http://pear.php.net/manual/ja/package.configuration.config.php
     * @see BEAR/test/files/example.ini
     * @see BEAR/test/files/example.xml
     * @see BEAR/test/files/example.php
     * @throws BEAR_Exception
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
            $cache = self::factory('BEAR_Cache');
            $cache->setLife(BEAR_Cache::LIFE_UNLIMITED);
            $key = $target . filemtime($target);
            $cacheResult = $cache->get($key);
            if ($cacheResult) {
                return $cacheResult;
            }
            // PEAR::Configを使って設定ファイルをパース
            $pathinfo = pathinfo($target);
            // 相対パスなら絶対パスに (/:linux :win)
            $target = (substr($target, 0, 1) == '/' || substr(
                $target,
                1,
                1
            ) == ':') ? $target : _BEAR_APP_HOME . '/App/Ro/' . $target;
            $extension = isset($options['extention']) ? $options['extention'] : $pathinfo['extension'];
            switch ($extension) {
                case 'yml':
                    if (function_exists('syck_load')) {
                        $content = file_get_contents($target);
                        $yaml = syck_load($content);
                    } else {
                        include_once 'BEAR/vendors/spyc-0.2.5/spyc.php';
                        $yaml = Spyc::YAMLLoad($target);
                    }
                    $cache->set($key, $yaml);
                    return $yaml;
                case 'csv':
                    $conf = File_CSV::discoverFormat($target);
                    $csv = array();
                    while ($fields = File_CSV::read($target, $conf)) {
                        array_push($csv, $fields);
                    }
                    $cache->set($key, $csv);
                    return $csv;
                case 'ini':
                    $parse = 'inicommented';
                    break;
                case 'xml':
                    $unserializer = new XML_Unserializer();
                    $unserializer->setOption('parseAttributes', true);
                    $xml = file_get_contents($target);
                    $unserializer->unserialize($xml);
                    $result = $unserializer->getUnserializedData();
                    return $result;
                    break;
                case 'php':
                    $parse = 'PHPConstants';
                    break;
                default:
                    return file_get_contents($target, FILE_TEXT);
                    break;
            }
            $config = new Config();
            $root = & $config->parseConfig($target, $parse);
            if (PEAR::isError($root)) {
                $msg = '設定を読み込む際のエラー: ';
                $msg .= $root->getMessage();
                $info = array('parse' => $parse, 'input' => $target);
                throw new BEAR_Exception($msg, compact('info'));
            } else {
                $result = $root->toArray();
                return $result['root'];
            }
        }
    }

    /**
     * コンフィグファイル読み込み
     *
     * @param      $target YAMLアプリケーション設定ファイルpath
     * @param bool $useApc
     *
     * @return array|mixed
     */
    public static function loadConfig($target, $useApc = false)
    {
        if ($useApc === true && function_exists('apc_fetch')) {
            $configKey = $target . filemtime($target);
            $config = apc_fetch($configKey, $isSuccess);
            if ($isSuccess === true) {
                return $config;
            }
        }
        $errorReporting = error_reporting();
        error_reporting(E_ALL);
        if (function_exists('yaml_parse_file')) {
            $appYaml = yaml_parse_file($target);
            $bearYaml = yaml_parse_file(_BEAR_BEAR_HOME . '/BEAR/BEAR/bear.yml');
        } else {
            include_once 'BEAR/vendors/spyc-0.2.5/spyc.php';
            $appYaml = Spyc::YAMLLoad($target);
            $bearYaml = Spyc::YAMLLoad(file_get_contents(_BEAR_BEAR_HOME . '/BEAR/BEAR/bear.yml'));
        }
        error_reporting($errorReporting); // for external code error
        $appYaml['core']['debug'] = ($appYaml['core']['debug']) ? true : false;
        // merge
        $appYaml = array_merge($bearYaml, $appYaml);
        foreach ($bearYaml as $key => $values) {
            if (isset($appYaml[$key])) {
                $appYaml[$key] = array_merge($bearYaml[$key], $appYaml[$key]);
            }
        }
        if (isset($configKey)) {
            apc_store($configKey, $appYaml);
        }
        return $appYaml;
    }

    /**
     * オブジェクト生成
     *
     * <pre>
     * 汎用ファクトリーメソッドです。生成したオブジェクトにfactoryという名前のメソッドが
     * 含まれるとそのメソッドが生成したオブジェクトを返します。
     *
     * Constructorの引き数はBEAR/bear.ymlの値にBEAR::init($config)で設定された値がマージされた
     * 値が使用されます。その値はBEAR::get('app');で取り出す事ができます。
     *
     * @param string $class   クラス名
     * @param array  $config  クラス用コンフィグ
     * @param array  $options オプション
     *
     * @return stdClass
     * @throws BEAR_Exception
     */
    public static function factory($class, array $config = array(), array $options = array())
    {
        // class
        $class = isset(self::$_registry['app'][$class]) && isset(self::$_registry['app'][$class]['__class']) ? self::$_registry['app'][$class]['__class'] : $class;
        // auto loader
        if (class_exists($class, false) === false) {
            try {
                self::onAutoload($class);
            } catch (Exception $e) {
                $info = compact('class');
                throw new BEAR_Exception("Auto loader failed for class [$class]", array(
                        'code' => self::CODE_BAD_REQUEST,
                        'info' => $info
                    ));
            }
        }
        // config;
        if (isset(self::$_registry['app'][$class])) {
            $config += self::$_registry['app'][$class];
        }
        $config = (isset($config['__config']) && is_callable($config['__config'])) ? call_user_func(
            $config['__config'],
            $config
        ) : $config;
        // merge common 'core' config
        $config += (array)self::$_registry['app']['core'];
        $object = new $class($config);
        // inject
        $injector = isset($options['injector']) ? $options['injector'] : ((isset(self::$_registry['app'][$class]) && isset(self::$_registry['app'][$class]['__injector']) ? self::$_registry['app'][$class]['__injector'] : 'onInject'));
        if (is_string($injector)) {
            if (method_exists($object, $injector)) {
                $object->$injector();
            } else {
                if (method_exists($object, 'onInject')) {
                    $object->onInject();
                }
            }
        } else {
            if (is_array($injector)) {
                if (is_callable(array($injector[0], $injector[1]))) {
                    $className = $injector[0];
                    $method = $injector[1];
                    call_user_func_array(array($className, $method), array($object, $config));
                } else {
                    throw new BEAR_Exception('Injector is not valid.', array(
                        'code' => self::CODE_BAD_REQUEST,
                        'info' => compact('injector')
                    ));
                }
            }
        }
        // factoryオブジェクトなら生成したインスタンスを返す
        if ($object instanceof BEAR_factory) {
            $object = $object->factory();
        }
        return $object;
    }

    /**
     * インスタンスの生成とサービスロケータの登録
     *
     * <pre>
     * 指定されたクラスのインスタンスをBEAR::factory()で生成してシングルトン管理します。
     *
     * Constructorの値はフレームワークデフォルト、アプリケーションデフォルト、$configの順でマージされたものが使われます。
     * 2 指定された初期化メソッド(デフォルトはonInject)がコールされ、
     *   メソッド内では通常そのオブジェクトが利用するオブジェクト（依存オブジェクト）をプロパティとして代入しオブジェクトの合成を行います。
     * 3 クラス名でレジストリに登録されその後シングルトンで扱われます。
     *
     * $configの変数の型によって動作が変わります。
     *
     * 配列の場合:　遅延生成
     *   $configが配列だとそのBEAR::factoryでその配列をコンストラクタに渡しオブジェクトを生成します。
     *   生成したオブジェクトはサービスプロパティにオブジェクトを格納します。
     *
     * 文字列の場合 :　サービスロケータ
     *   サービスロケーターの呼び出しとして機能します。与えられた文字列をキーにレジストリからオブジェクトを返します。
     *   レジストリにオブジェジェクトの代わりに配列($class, $config)でが入っているとその情報でインスタンス化します。
     *   これはlazy-load(遅延ロード）を実現するものです。実際の読み込みまでインスタンス生成コストがかかりません。
     *
     * オブジェクトの場合 :
     *   そのオブジェクトをそのまま返します。
     *
     * $options
     *   booleanの場合: (deprecated)
     *     trueだとsingleton生成、falseだとprototype生成（毎回new)になります。
     *
     *   arrayの場合:
     *      'is_singleton' bool  singleton指定 (deprecated: factory()推奨)
     * Inject結果をキャッシュするかの指定。キャッシュ時間は無期限です。
     * Inject後のオブジェクトがシリアライズされキャッシュ利用されます。
     *      'inector'     string インジェクター指定。デフォルトは'onInject'
     * </pre>
     *
     * @param string $class   クラス名
     * @param mixed  $config  array=クラスコンフィグ string=サービスキー　object=そのまま返る
     * @param mixed  $options (array) オプション　(bool) deprecated
     *
     * @return stdClass
     *
     * @see Core J2EE Patterns - Service Locator
     * @see http://java.sun.com/blueprints/corej2eepatterns/Patterns/ServiceLocator.html
     * @throws BEAR_Exception
     */
    public static function dependency($class, $config = array(), $options = true)
    {
        // 代入
        if (is_object($config)) {
            return $config;
        }
        // シングルトン
        if (isset(self::$_registry[$class])) {
            if (is_array(self::$_registry[$class])) {
                self::$_registry[$class] = self::factory(self::$_registry[$class][0], self::$_registry[$class][1]);
            }
            return self::$_registry[$class];
        }
        // persistent
        if (isset($options['persistent']) && $options['persistent'] !== false) {
            $cache = self::dependency('BEAR_Cache');
            $object = $cache->get($class);
            if (is_object($object)) {
                return $object;
            }
        }
        // サービスロケーター
        if (is_string($config)) {
            if (!isset(self::$_registry[$config])) {
                $msg = 'Service is not exists';
                $info = array('service' => $config);
                throw new BEAR_Exception($msg, compact('info'));
            }
            $service = self::$_registry[$config];
            if (is_object($service)) {
                return self::$_registry[$config];
            } else {
                if (is_array($service)) {
                    // 遅延ロード
                    self::$_registry[$service[0]] = self::factory($service[0], $service[1]);
                    return self::$_registry[$service[0]];
                } else {
                    $msg = 'No service in self::dependency';
                    $info = array('class' => $class);
                    throw new BEAR_Exception($msg, array('info' => $info));
                }
            }
        }
        if (is_array($options)) {
            $options = array_merge(array('is_singleton' => true, 'inject' => 'onInject'), $options);
        } else {
            $options = array('is_singleton' => $options);
        }
        //オブジェクト作成
        if ($options['is_singleton']) {
            // シングルトン
            $object = isset(self::$_registry[$class]) ? self::$_registry[$class] : false;
            if ($object !== false) {
                if ($object instanceof BEAR_Base) {
                    $object->setConfig($config);
                }
            } else {
                $object = self::factory($class, $config, $options);
                self::$_registry[$class] = $object;
                if (isset($options['persistent']) && $options['persistent'] !== false) {
                    $cache->set($class, $object);
                }
            }
        } else {
            $object = self::factory($class, $config, $options);
        }
        return $object;
    }

    /**
     * オブジェクトをレジストリにセット
     *
     * オブジェクトをレジストリにセットします。
     * array($class, $config)というフォーマットでセットするとlazy-load（遅延読み込み）になり読み込み時に$configを引数にインスタンス化されます。
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
     * @throws BEAR_Exception
     */
    public static function set($key, $service)
    {
        if (self::exists($key)) {
            $msg = 'Service Already Exists （self::dependencyでキーがすでに登録されています)';
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
     * @return stdClass
     * @throws BEAR_Exception
     */
    public static function get($key)
    {
        if (!isset(self::$_registry[$key])) {
            $msg = "Service[{$key}] is not Exists";
            $info = array('service key' => $key);
            throw new BEAR_Exception($msg, array('info' => $info));
        }
        if (is_array(self::$_registry[$key])) {
            self::$_registry[$key] = self::factory(self::$_registry[$key][0], self::$_registry[$key][1]);
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
     * レジストリの内容を全て取得
     *
     * @return array
     */
    public static function getAll()
    {
        return self::$_registry;
    }
}
