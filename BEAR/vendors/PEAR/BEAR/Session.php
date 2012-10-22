<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Session
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Session.php 1205 2009-11-10 14:49:52Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Session/BEAR_Session.html
 */
/**
 * セッションクラス
 *
 * <pre>
 * セッションを取り扱います。
 * PEAR::HTTP_Session2を利用していて、デフォルトのファイルセッション、
 * webクラスターシステムのためのDBまたはmemchacheが選択できます。
 * また詳細設定は　htdocs/.htaccess(またはphp.ini)でも行う必要があります。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Session
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Session.php 1205 2009-11-10 14:49:52Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Session/BEAR_Session.html
 */
class BEAR_Session extends BEAR_Base
{

    const SESSION_KEY = '_s';

    /**
     * セッション不使用
     */
    const ADAPTOR_NONE = 0;

    /**
     * ファイルセッション（クラスター不可）
     */
    const ADAPTOR_FILE = 1;

    /**
     * DBセッション
     */
    const ADAPTOR_DB = 2;

    /**
     * memchacheセッション
     */
    const ADAPTOR_MEMCACHE = 3;

    /**
     * セッショントークン
     */
    const SESSION_TOKEN = 'stoken';

    /**
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * セッションスタート
     *
     * @return bool
     */
    private function _start()
    {
        static $hasStarted = false;

        if ($hasStarted || $this->_config['adaptor'] === self::ADAPTOR_NONE) {
            return;
        }
        $this->_setAdpator($this->_config);
        // セッションスタート
        HTTP_Session2::start(self::SESSION_KEY);
        // セッションを通じた固定トークン
        if (HTTP_Session2::isNew()) {
            HTTP_Session2::set(BEAR_Session::SESSION_TOKEN, substr(md5(uniqid()), 0, 4));
        }
        // 有効期限
        if (isset($this->_config['expire'])) {
            //            HTTP_Session2::setExpire($this->_config['expire'], true);
        }
        // アイドル時間
        if (isset($this->_config['idle'])) {
            //            HTTP_Session2::setIdle($this->_config['idle'], true);
        }
        $this->_log->log('Session Start', array('id' => session_id(),
            'module' => session_module_name() . '/' . $this->_config['adaptor']));
        $hasStarted = true;
    }

    /**
     * セッションクラス初期化
     *
     * セッションエンジンを指定し初期化します。
     *
     * @return void
     * @static
     * @ignore
     */
    private function _setAdpator($config)
    {
        //セッションハンドラ初期化
        switch ($config['adaptor']) {
        case self::ADAPTOR_MEMCACHE :
            ini_set("session.save_handler", 'memcache');
            ini_set("session.save_path", $config['session']['path']);
            break;
        case self::ADAPTOR_DB :
            HTTP_Session2::useTransSID(false);
            HTTP_Session2::useCookies(true);
            // DSN を指定します
            HTTP_Session2::setContainer('MDB2', array(
                'dsn' => $config['path'],
                'table' => 'sessiondata',
                'autooptimize' => true));
            break;
        case self::ADAPTOR_FILE :
            // ファイルセッションに関するPHPのバグ対応
            // @link http://bugs.php.net/bug.php?id=25876
            ini_set('session.save_handler', 'files');
            if (isset($config['path'])) {
                ini_set("session.save_path", $config['session']['path']);
            } else {
                ini_set("session.save_path", _BEAR_APP_HOME . '/tmp/session');
            }
            break;
        case self::ADAPTOR_NONE :
            break;
        default :
            // error
            $msg = 'Invalid Session Engine.';
            $info = array('adaptor' => $config['adaptor']);
            throw $this->_exception($msg, array('info' => $info));
        }
    }

    /**
     * セッション変数取得
     *
     * <pre>
     * セッション変数を取得します。変数の無い場合に$defaultを指定することができます
     * </pre>
     *
     * @param string $key     セッション変数名
     * @param string $default デフォルト
     *
     * @return   mixed   セッション変数
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
     * <pre>
     * セッション変数をセットします。
     * </pre>
     *
     * @param string $key    セッションキー
     * @param mixed  $values 値
     *
     * @return mm
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
     * <pre>
     * セッション変数を消去します。
     * </pre>
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
}
