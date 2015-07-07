<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Agent.php 2567 2011-06-19 16:17:11Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net/
 */

/**
 * エージェント
 *
 * <pre>
 * UAコードの確定やUAコードに応じたAgentアダプターをセットします。
 * 確定したUAコードはUAスニッフィングやUA別のインジェクター等に用いられます。
 * UAコードはBEARの自動判別の他に$config['ua_inject']に外部インジェクタークラスを指定してアプリケーションがUAコードをインジェクトすることがあります。
 * BEAR_Agent_Adapter_(UAコード）でUAアダプタークラスが用意されてないものはグローバルレジストリに先にセットしておきます。
 * AgentアダプターはBEAR_Agent_Adapter_*で定義されUAの継承関係や、ビューの時のconfigを設定します。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Agent.php 2567 2011-06-19 16:17:11Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net/
 *
 * @Singleton
 *
 * @config  string user_agent ユーザーエージェント（省略すれば$_SERVER['HTTP_USER_AGENT')
 * @config  string ua_inject  外部UAインジェクター
 */
class BEAR_Agent extends BEAR_Base
{
    /**
     * PC
     *
     * @var string
     */
    const UA_DEFAULT = 'Default';

    /**
     * Mobile
     *
     * @var string
     */
    const UA_MOBILE = 'Mobile';

    /**
     * Docomo iモード
     *
     * @var string
     */
    const UA_DOCOMO = 'Docomo';

    /**
     * AU Ezweb
     *
     * @var string
     */
    const UA_EZWEB = 'Ezweb';

    /**
     * AU Ezweb
     *
     * @var string
     * @deprecated
     * @ignore
     */
    const UA_AU = 'Au';

    /**
     * Softbank 3GC
     *
     * @var string
     */
    const UA_SOFTBANK = 'Softbank';

    /**
     * BOTクライアント
     *
     * @var string
     */
    const UA_BOT = 'Bot';

    /**
     * Apple
     *
     * @var string
     */
    const UA_APPLE = 'Apple';

    /**
     * Apple iPhone/iPod touch
     *
     * @var string
     */
    const UA_IPHONE = 'Iphone';

    /**
     * Apple iPad
     *
     * @var string
     */
    const UA_IPAD = 'Ipad';

    /**
     * Google Android
     *
     * @var string
     */
    const UA_ANDROID = 'Android';

    public $agentMobile;

    /**
     * モバイルエージェント
     *
     * PEAR::Net_UserAgent_Mobileオブジェクト
     *
     * @var mixed
     */
    protected $_agentMobile = array('user_agent' => null);

    /**
     * @var BEAR_Agent_Adapter
     */
    public $adapter;

    /**
     * UAコード
     *
     * @var string
     */
    protected $_ua = self::UA_DEFAULT;

    /**
     * Inject
     *
     * user_agentによって以下のプロパティを注入します。
     * 独自のエージェント判別ロジックを入れたいときはインジェクタを変更します。
     *
     * Net_UserAgent_Mobile agentMobile
     * string               _ua UAコード
     * mixed                adapter エージェントアダプター
     *
     * @return void
     */
    public function onInject()
    {
        $this->_agentMobile = $this->_config;
        $injectUa = isset($this->_config['ua_inject']) && is_callable(
            array($this->_config['ua_inject'], 'inject')
        ) ? $this->_config['ua_inject'] : 'BEAR_Agent_Ua';
        // _uaを注入
        call_user_func(array($injectUa, 'inject'), $this, $this->_config);
        $this->_config['ua'] = $this->_ua;
        try {
            $this->adapter = BEAR::dependency('BEAR_Agent_Adapter_' . $this->_ua, $this->_config);
        } catch (Exception $e) {
            $this->adapter = BEAR::dependency('BEAR_Agent_Adapter_Default', $this->_config);
        }
    }

    /**
     * __toString
     *
     * 文字列として扱うとUAコードを返す
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_ua;
    }

    /**
     * ユーザーエージェントコードの取得
     *
     * @return string
     */
    public function getUa()
    {
        return $this->_ua;
    }

    /**
     * 携帯のユニークIDを取得
     *
     * @return string
     */
    public function getSerialNumber()
    {
        $serial = '';
        $this->agentMobile = BEAR::dependency('BEAR_Agent_Mobile', $this->_agentMobile);
        switch ($this->_ua) {
            case self::UA_DOCOMO:
                /** @noinspection PhpUndefinedMethodInspection */
                /** @noinspection PhpUndefinedMethodInspection */
                $serial = $this->agentMobile->getCardID();
                if ($serial === '') {
                    /** @noinspection PhpUndefinedMethodInspection */
                    /** @noinspection PhpUndefinedMethodInspection */
                    $serial = $this->agentMobile->getSerialNumber();
                }
                break;
            case self::UA_EZWEB:
                /** @noinspection PhpUndefinedMethodInspection */
                /** @noinspection PhpUndefinedMethodInspection */
                $serial = $this->agentMobile->getHeader('X-UP-SUBNO');
                break;
            case self::UA_SOFTBANK:
                /** @noinspection PhpUndefinedMethodInspection */
                /** @noinspection PhpUndefinedMethodInspection */
                $serial = $this->agentMobile->getSerialNumber();
                break;
            default:
                // error
                break;
        }

        return $serial;
    }

    /**
     * 画面サイズの縦、横のサイズを取得
     *
     * @return array　array(width, height)
     */
    public function getDisplaySize()
    {
        static $size;

        if (!isset($size)) {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            $size = BEAR::dependency('BEAR_Agent_Mobile', $this->_agentMobile)->getDisplay()->getSize();
        }

        return $size;
    }

    /**
     * 携帯の表示可能文字数を取得
     *
     * 携帯の表示可能文字数を取得します。。
     *
     * @return array　array(width, height)
     */
    public function getDisplayByteSize()
    {
        static $byteSize;

        if (!isset($byteSize)) {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            $display = BEAR::dependency('BEAR_Agent_Mobile', $this->_agentMobile)->getDisplay();
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedMethodInspection */
            $byteSize = array($display->getWidthBytes(), $display->getHeightBytes());
        }

        return $byteSize;
    }

    /**
     * エージェントロールの取得
     *
     * @return array
     */
    public function getAgentRole()
    {
        $adapterConfig = $this->adapter->getConfig();

        return $adapterConfig['role'];
    }

    /**
     * エージェントロールに対応したファイルを取得
     *
     * <pre>
     * 配列でロールに応じたファイルを返します
     *
     * ex)
     * roleが'Docomo'の場合
     *
     * index.docomo.html
     * index.mobile.html
     * index.html
     *
     * というファイルに順にスキャンしてあればそれが使われます。
     *
     * @param string $dir          ディレクトリパス
     * @param string $fileNameBase 拡張子なしファイル名
     * @param string $ext          ファイル名拡張子
     *
     * @return string
     */
    public function getRoleFile($dir, $fileNameBase, $ext = 'tpl')
    {
        $role = $this->getAgentRole();
        $result = $dir . '/' . $fileNameBase . '.' . $ext;
        foreach ($role as $uaCode) {
            $fullPath = $dir . '/' . $fileNameBase . '.' . strtolower($uaCode) . '.' . $ext;
            if (file_exists($fullPath)) {
                $result = $fullPath;
                break;
            }
        }

        return $result;
    }
}
