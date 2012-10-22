<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Session
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Session.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * セッション
 *
 * セッションを取り扱います。
 * PEAR::HTTP_Session2を利用していて、デフォルトのファイルセッション、
 * webクラスターシステムのためのDBまたはmemchacheが選択できます。
 * 詳細設定は　htdocs/.htaccess(またはphp.ini)でも行う必要があります。
 *
 * @category  BEAR
 * @package   BEAR_Session
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Session.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 *
 * @Singleton
 *
 * @config mixed    adapter  アダプター
 * @config string   path     ファイルパス(file) または DSN(DB)
 * @config int      idle     アイドル時間（秒）
 * @config int      expire   タイムアウト（秒）
 * @config callable callback アイドル時間の切れたときのコールバック
 */
class BEAR_Session extends BEAR_Base
{
    /**
     * セッション不使用
     */
    const ADAPTER_NONE = 0;

    /**
     * ファイルセッション（クラスター不可）
     */
    const ADAPTER_FILE = 1;

    /**
     * DBセッション
     */
    const ADAPTER_DB = 2;

    /**
     * memchacheセッション
     */
    const ADAPTER_MEMCACHE = 3;

    /**
     * @var BEAR_Log
     */
    protected $_log;
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['prefix'] = $this->_config['prefix'] ? $this->_config['prefix'] : ($this->_config['info']['id'] . $this->_config['info']['version'] . (int)$this->_config['debug']);
    }

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * セッションスタート
     *
     * @return void
     */
    private function _start()
    {
        static $hasStarted = false;

        if ($hasStarted !== false
            || $this->_config['adapter'] === self::ADAPTER_NONE
        ) {
            return;
        }
        $hasStarted = true;
        $this->_setAdpator($this->_config);
        HTTP_Session2::start(null);
        // セッションを通じた固定トークン
        if (HTTP_Session2::isNew()) {
            BEAR::dependency('BEAR_Form_Token')->newSessionToken();
        }
        // 有効期限
        if (isset($this->_config['idle']) && $this->_config['idle']) {
            HTTP_Session2::setIdle($this->_config['idle']);
            // セッションの期限切れ
            if (HTTP_Session2::isIdle()) {
                if (isset($this->_config['callback']) && is_callable($this->_config['callback'])) {
                    $method = $this->_config['callback'][1];
                    BEAR::dependency($this->_config['callback'][0], array())->$method();
                } else {
                    // コールバック指定がないとセッション破壊
                    HTTP_Session2::destroy();
                }
            } else {
                HTTP_Session2::updateIdle();
            }
        }
        // セッションを開始してから（=最初のアクセス）からセッションが切れる時間を指定
        if (isset($this->_config['expire']) && $this->_config['expire']) {
            HTTP_Session2::setExpire($this->_config['expire'], false);
            if (HTTP_Session2::isExpired()) {
                if (isset($this->_config['expire_callback']) && is_callable($this->_config['expire_callback'])) {
                    $method = $this->_config['expire_callback'][1];
                    BEAR::dependency($this->_config['expire_callback'][0], array())->$method();
                } else {
                    // コールバック指定がないとセッション破壊
                    HTTP_Session2::destroy();
                }
            }
        }
        // GCが働くまでの時間
        if (isset($this->_config['gc_max_lifetime']) && $this->_config['gc_max_lifetime']) {
            HTTP_Session2::setGcMaxLifeTime($this->_config['gc_max_lifetime']);
        }
        // セッションスタート
        $this->_log->log(
            'Session Start',
            array(
                'id' => session_id(),
                'module' => session_module_name() . '/' . $this->_config['adapter']
            )
        );
    }

    /**
     * セッションアダプターのセット
     *
     * @param array $config
     *
     * @return void
     * @throws BEAR_Session_Exception
     */
    private function _setAdpator(array $config)
    {
        // セッションハンドラ初期化
        switch ($config['adapter']) {
            case self::ADAPTER_MEMCACHE:
                ini_set("session.save_handler", 'memcache');
                ini_set("session.save_path", $config['path']);
                break;
            case self::ADAPTER_DB:
                // DSN を指定します
                $config = array(
                    'dsn' => $config['path'],
                    'table' => 'sessiondata',
                    'autooptimize' => true
                );
                HTTP_Session2::setContainer('MDB2', $config);
                break;
            case self::ADAPTER_FILE:
                if (isset($config['path']) && file_exists($config['path'])) {
                    ini_set("session.save_path", $config['path']);
                }
                break;
            case self::ADAPTER_NONE:
                // no cache
                break;
            default:
                // error
                $msg = 'Invalid Session Engine.';
                $info = array('adapter' => $config['adapter']);
                throw $this->_exception($msg, array('info' => $info));
        }
    }

    /**
     * セッション変数取得
     *
     * セッション変数を取得します。変数の無い場合に$defaultを指定することができます
     *
     * @param string $key     セッション変数名
     * @param string $default デフォルト
     *
     * @return mixed セッション変数
     * @static
     */
    public function &get($key, $default = null)
    {
        $this->_start();
        $key = $this->_config['prefix'] . $key;
        $values = HTTP_Session2::get($key, $default);
        $this->_log->log('Session[R]', array($key));
        return $values;
    }

    /**
     * セッション変数セット
     *
     * @param string $key    セッションキー
     * @param mixed  $values 値
     *
     * @return void
     */
    public function set($key, $values)
    {
        $this->_start();
        $key = $this->_config['prefix'] . $key;
        $return = HTTP_Session2::set($key, $values);
        $log = array('name' => $key, 'val' => $values, 'return' => $return);
        $this->_log->log('Session[W]', $log);
    }

    /**
     * セッション変数マージ
     *
     * 既存の値とマージしてセッション保存します。
     *
     * @param string $key    キー
     * @param mixed  $values 値
     *
     * @return void
     */
    public function merge($key, $values)
    {
        $this->_start();
        $key = $this->_config['prefix'] . $key;
        $old = HTTP_Session2::get($key);
        if (is_array($old)) {
            $values = array_merge_recursive($old, $values);
        }
        $return = HTTP_Session2::set($key, $values);
        $log = array('key' => $key, 'val' => $values, 'result' => $return);
        $this->_log->log('Session[Merge]', $log);
    }

    /**
     * セッション変数消去
     *
     * @param string $key セッションキー
     *
     * @return void
     */
    public function unregister($key)
    {
        $this->_start();
        HTTP_Session2::unregister($this->_config['prefix'] . $key);
        $this->_log->log('Session[DEL]', array('name' => $key));
    }

    /**
     * セッション開始
     *
     * @return void
     */
    public function start()
    {
        $this->_start();
    }

    /**
     * アイドル更新
     *
     * @return void
     */
    public function updateIdle()
    {
        HTTP_Session2::updateIdle();
    }

    /**
     * セッション破棄
     *
     * @return void
     */
    public function destroy()
    {
        HTTP_Session2::destroy();
    }
}
