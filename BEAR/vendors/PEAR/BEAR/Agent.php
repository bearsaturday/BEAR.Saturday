<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Agent.php 1205 2009-11-10 14:49:52Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
 */
/**
 * エージェントクラス
 *
 * <pre>
 * 各エージェント（携帯）判別やエージェント毎の詳細情報を取得するのに用います。
 * PEARのNet_UserAgent_Mobileオブジェクトをプロパティに保持しています。
 * </pre>
 *
 * I, Docomo
 * E, EZweb
 * S, SoftBank
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Agent.php 1205 2009-11-10 14:49:52Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
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
     * AU EzWeb
     *
     * @var string
     */
    const UA_EZWEB = 'Ezweb';

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
     * Apple iPhone/iPod touch
     *
     * @var string
     */
    const UA_IPHONE = 'Iphone';

    /**
     * Google Android
     *
     * @var string
     */
    const UA_ANDROID = 'Android';

    /**
     * ボット用携帯エージェント(Docomo)
     */
    const BOT_DOCOMO = 'DoCoMo/2.0 N900i(c100;TB;W24H12)';

    /**
     * ボット用携帯エージェント(Docomo)
     */
    const BOT_AU = 'KDDI-TS26 UP.Browser/6.2.0.5 (GUI) MMP/1.1';

    /**
     * ボット用携帯エージェント(SB)
     */
    const BOT_SOFTBANK = 'SoftBank/1.0/705P/PJA23 Browser/Teleca-Browser/3.1 Profile/MIDP-2.0 Configuration/CLDC-1.1';

    /**
     * ボット用携帯エージェント(Willcom)
     */
    const BOT_WILLICOM = 'Mozilla/3.0(WILLCOM;KYOCERA/WX310K/2;1.1.5.15.000000/0.1/C100) Opera 7.0';


    /**
     * モバイレルかどうか
     *
     * @var bool
     */
    public static $isMobile = false;

    /**
     * BOTかどうか
     *
     * @var bool
     */
    public static $isBot = false;

     /**
     * モバイルエージェント
     *
     * PEAR::Net_UserAgent_Mobileオブジェクト
     *
     * @var     string
     */
    public $agentMobile;

    /**
     * @deprecated
     */
    private $_isMobile = false;

    /**
     * UAコード
     *
     * @var string
     */
    protected $_ua = self::UA_DEFAULT;

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
     * Net_UserAgent_Mobileクラスのインスタンス生成
     *
     * <pre>
     * Net_UserAgent_Mobileオブジェクトを生成してagentプロパティに格納します
     * </pre>
     *
     * @param string $agent エージェント文字列
     *
     * @return  object
     */
    public function onInject()
    {
        $this->injectUa();
        $this->_config['ua'] = $this->_ua;
        try {
        	$this->adaptor = BEAR::dependency('BEAR_Agent_Adaptor_' . $this->_ua, $this->_config);
        } catch (Exception $e) {
        	$this->adaptor = BEAR::dependency('BEAR_Agent_Adaptor_Default', $this->_config);
        }
    }

    /**
     * UAの注入
     *
     */
    public function injectUa()
    {
        $httpUserAgent = isset($this->_config['user_agent']) ? $this->_config['user_agent'] : null;
        $this->agentMobile = $this->agent = &Net_UserAgent_Mobile::factory($httpUserAgent);
        if (PEAR::isError($this->agent)) {
            switch (true) {
            case (strstr($httpUserAgent, 'DoCoMo')) :
                $botAgent = self::BOT_DOCOMO;
                break;
            case (strstr($httpUserAgent, 'KDDI-')) :
                $botAgent = self::BOT_AU;
                break;
            case (preg_match('/(SoftBank|Vodafone|J-PHONE|MOT-)/', $httpUserAgent)) :
                $botAgent = self::BOT_SOFTBANK;
                break;
            case (preg_match('/(DDIPOCKET|WILLCOM)/', $httpUserAgent)) :
                $botAgent = self::BOT_WILLCOM;
                break;
            default :
                $botAgent = '';
            }
            $this->agentMobile = &Net_UserAgent_Mobile::factory($botAgent);
            $this->isBot = true;
        }
        if ($this->agentMobile->isNonMobile()) {
	        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) {
	           // iPhoneの場合
	        	$this->_ua = self::UA_IPHONE;
	        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false) {
	            // Androidの場合
               $this->_ua = self::UA_ANDROID;
	        } else {
               $this->_ua = self::UA_DEFAULT;
	        }
        } else {
        	$this->_ua = ucwords(strtolower($this->agentMobile->getCarrierLongName()));
        	$this->_isMobile = true;
        }
    }
    //public functon injectUa()
    //{}

    /**
     * ユーザーエージェントコードの取得
     *
     * @return string
     *
     */
    public function getUa()
    {
    	return $this->_ua;
    }

    public function isMobile()
    {
    	return $this->_isMobile;
    }

    /**
     * 携帯のユニークIDを取得
     *
     * @return string
     */
    public function getSerialNumber()
    {
        $serial = '';
        switch ($this->_ua) {
        case BEAR_Agent::UA_DOCOMO :
            $serial = $this->agentMobile->getCardID();
            if ($serial === '') {
                $serial = $this->agentMobile->getSerialNumber();
            }
            break;
        case BEAR_Agent::UA_AU :
            $serial = $this->agentMobile->getHeader('X-UP-SUBNO');
            break;
        case BEAR_Agent::UA_SOFTBANK :
            $serial = $this->agentMobile->getSerialNumber();
            break;
        default :
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
            $display = $this->agent->getDisplay();
            $size = $display->getSize();
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
            $display = $this->agentMobile->getDisplay();
            $byteSize = array($display->getWidthBytes(),
                $display->getHeightBytes());
        }
        return $byteSize;
    }

    /**
     * キャッシュサイズの取得
     *
     * 端末のキャッシュサイズをバイト数で返します。
     *
     * @return int
     */
    public function getCacheSize()
    {
        switch ($this->_ua) {
        case BEAR_Agent::UA_DOCOMO :
            $size = $this->agentMobile->getCacheSize() * 1024;
            break;
        case BEAR_Agent::UA_AU :
            $headers = getallheaders();
            $size = $headers['x-up-devcap-max-pdu'];
            break;
        case BEAR_Agent::UA_SOFTBANK :
            $phone = $this->agentMobile->getName(); // 'J-PHONE'
            $version = (int)$this->agentMobile->getVersion(); // 2.0
            if ($phone == 'J-PHONE') {
                if ($version <= 3.0) {
                    $size = 6000;
                } elseif ($version <= 4.2) {
                    $size = 12000;
                } elseif ($version <= 4.3) {
                    $size = 30000;
                } elseif ($version <= 5.0) {
                    $size = 200000;
                } else {
                    $size = 200000;
                }
            } else {
                $size = 300000;
            }
            break;
        default :
            $size = false;
        }
        return $size;
    }

    /**
     * セッションクエリーを付加
     *
     * クッキーが使えないエージェント用にURLからセッションクエリー
     * 付きURLを返します。
     *
     * @param string $url URL
     *
     * @return string
     */
    public function getSessionQuery($url = false)
    {
        $sessionName = session_name();
        $sessionId = session_id();
        if (!isset($_COOKIE[$sessionName])) {
            /**
             * セッションクエリーが付いてれば消去
             */
            $url = preg_replace("/&*{$sessionName}=[^&]+/is", '', $url);
            $con = ($url && strpos($url, "?")) ? '&' : '?';
            $url .= "{$con}{$sessionName}={$sessionId}";
        }
        return $url;
    }
}