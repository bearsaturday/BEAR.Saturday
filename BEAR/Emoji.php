<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * 絵文字クラス
 *
 * <pre>
 * 絵文字を取り扱うためのクラスです。
 * 携帯の絵文字のキャリア相互変換、imgタグによる絵文字の画像表示が行えます。
 * ソフトバンクモバイルの3G端末、旧端末どちらもサポートします。
 * UTF8絵文字からwebコード絵文字の変換が行えます。
 * 絵文字変換はメール時の変換にキャリアが仕様してる変換マップに基づいています。
 * 対応する絵文字が無い場合はカタカナで表現されます。
 * </pre>
 *
 * @Singleton
 *
 * @cofing string ua     ユーザーエージェントコード（省略可）
 * @config string submit 絵文字サブミット 'pass' | 'entity' | 'remove
 */
class BEAR_Emoji extends BEAR_Base
{
    /**
     * iモード絵文字10進数エンティティ開始番号
     */
    const DOCOMO_MIN = 63647; // 0xF89F

    /**
     * iモード絵文字10進数エンティティ終了番号
     */
    const DOCOMO_MAX = 63996; // 0xF9FC

    /**
     * Ez絵文字10進数エンティティ開始番号
     */
    const EZWEB_MIN = 62272; // 0xF340

    /**
     * Ez絵文字10進数エンティティ終了番号
     */
    const EZWEB_MAX = 63484; // 0xF7FC

    /**
     * Softbank絵文字10進数エンティティ開始番号
     */
    const SOFTBANK_MIN = 0xE001;

    /**
     * Softbank絵文字10進数エンティティ終了番号
     */
    const SOFTBANK_MAX = 0xE537;

    /**
     * 入力文字列
     *
     * @var string
     */
    private $_string;

    /**
     * 携帯キャリアコード
     *
     * @var string
     */
    private static $_uaShortCode;

    /**
     * エージェントコード
     *
     * @var string
     */
    private $_ua;

    /**
     * コールバック関数用config
     *
     * *Any better idea ?*
     *
     * @var array
     */
    private static $_staticConfig = array();

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (! isset($config['ua'])) {
            $config['ua'] = BEAR::dependency('BEAR_Agent')->getUa();
        }
        parent::__construct($config);
        self::$_staticConfig = $this->_config;
    }

    /**
     * Inject
     */
    public function onInject()
    {
        $this->_ua = (isset($this->_config['ua'])) ? $this->_config['ua'] : BEAR::dependency('BEAR_Agent')->getUa();
        $this->_string = isset($this->_config['string']) ? $this->_config['string'] : '';
    }

    /**
     * SB3G端末の絵文字入りUTF-8コードの文字列をwebコードの絵文字文字列に変換
     *
     * <pre>SB3G端末からポストされた絵文字入りのユニコード文字列を
     * webコードのUTF-8に変換します。
     * (注意）3G端末のSJISページでは絵文字はポストされない。
     * SBで絵文字を取り扱うにはUTF8ページを用意する必要があります。
     * </pre>
     *
     * @param string $utfEight 文字列
     *
     * @return string
     * @static
     */
    public function encodeWebCode($utfEight)
    {
        $result = array();
        $unicodes = I18N_UnicodeString::utf8ToUnicode($utfEight);
        foreach ($unicodes as $unicodeDecimal) {
            // 絵文字処理
            if (($unicodeDecimal > 0xE000 && $unicodeDecimal <= 0xE53E)) {
                if (($unicodeDecimal > 0xE000 && $unicodeDecimal < 0xE100)) {
                    $offset = hexdec('98E0');
                } elseif ($unicodeDecimal > 0xE100 && $unicodeDecimal < 0xE300) {
                    $offset = hexdec('9BE0');
                } else {
                    $offset = hexdec('93E0');
                }
                $webcodeDecimal = $unicodeDecimal - $offset;
                // 絵文字
                // 1B24 (ESC開き)
                $result[] = 0x1b;
                $result[] = 0x24;
                // 絵文字2バイトコード
                $result[] = ($webcodeDecimal >> 8) & 0xff;
                $result[] = $webcodeDecimal & 0xff;
                // 0F (ESC閉じ)
                $result[] = 0x0f;
            } else {
                // 非絵文字
                $result[] = $unicodeDecimal;
            }
        }
        // unicodeからUTF8に
        $iEighteen = new I18N_UnicodeString($result, 'Unicode');
        $result = $iEighteen->toUtf8String();

        return $result;
    }

    /**
     * 10進数エンティティをつくる
     *
     * stringプロパティの文字列を10進エンティティにしてentityプロパティに格納して返します
     *
     * @param $string
     *
     * @return string
     */
    public function makeDecEntity($string)
    {
        static $converterMap = null;
        switch ($this->_config['ua']) {
            case BEAR_Agent::UA_SOFTBANK:
                if ($converterMap === null) {
                    $converterMap = $this->_getEmojiMap($this->_config['ua']);
                }
                $unicodes = I18N_UnicodeString::utf8ToUnicode($string);
                $iEighteen = new I18N_UnicodeString($unicodes, 'Unicode');
                $string = $iEighteen->toUtf8String();
                $string = mb_encode_numericentity($string, $converterMap, 'utf-8');
                break;
            case BEAR_Agent::UA_DOCOMO:
                $emojiRegex = '[\xF8\xF9][\x40-\x7E\x80-\xFC]';
                $string = $this->_makeEntityBySjisRegex($string, $emojiRegex);
                break;
            case BEAR_Agent::UA_EZWEB:
                // AUの文字範囲
                // F340～F3FC
                // F440～F493
                // F640～F6FC
                // F740～F7FC
                $emojiRegex = '[\xF3\xF6\xF7][\x40-\xFC]|[\xF4][\x40-\x93]';
                $string = $this->_makeEntityBySjisRegex($string, $emojiRegex);
                break;
            default:
                trigger_error('Agent is not mobile.', E_USER_NOTICE);
                break;
        }

        return $string;
    }

    /**
     * 16進エンティティをつくる
     *
     *  10進エンティティから16進エンティティをつくります。
     * _stringプロパティの文字列を10進エンティティにして
     * _stringプロパティに格納して返します
     *
     * @param $string
     *
     * @return mixed
     */
    public function makeHexEntity($string)
    {
        $regex = '/&#(\d{5});/is';
        $string = preg_replace_callback(
            $regex,
            array(__CLASS__, '_onHexEntity'),
            $string
        );

        return $string;
    }

    /**
     * 絵文字変換
     *
     * 絵文字変換マップを使って10進エンティティから
     * 他キャリアの対応する絵文字に変換します。
     *
     * @param mixed $to デフォルトはエージェント
     *
     * @return string
     */
    public function convert($to = false)
    {
        //10進エンティティに
        $this->makeDecEntity();
        $toRef = &PEAR::getStaticProperty(__CLASS__, 'to');
        //toを保存
        $toRef = ($to) ? $to : self::$_uaShortCode;
        //変換
        if ($this->_ua == BEAR_Agent::UA_SOFTBANK) {
            $regex = '/\x1b\x24[GEFOPQ][\x21-\x7a]*\x0f/is';
        } else {
            $regex = '/&#(\d{5});/is';
        }
        $this->_string = preg_replace_callback($regex, array(__CLASS__, '_onConvertEmoji'), $this->_string);

        return $this->_string;
    }

    /**
     * エンティティ化されている絵文字を含んだ文字列をイメージタグに変換します
     *
     * smartyのoutputフィルターなどに使用します。$uaで指定したエージェント
     * (無指定の場合は使用しているエージェント）の絵文字はバイナリ出力されます。
     * PC(_BAER_UA_DEFAULT)を指定すると全ての絵文字がイメージタグ表示されます。
     *
     * @param $string
     *
     * @return mixed
     */
    public function convertEmojiImage($string)
    {
        //Docomo/Au変換
        $string = preg_replace_callback('/&#(\d+);/is', array(__CLASS__, '_onEmojiImage'), $string);

        return $string;
    }

    //    /**
    //     * AU絵文字変換
    //     *
    //     * <pre>local img形式のAU絵文字を10進エンティティ表記に変換します。</pre>
    //     *
    //     * @return string
    //     */
    //    public function localimg2entity($string)
    //    {
    //        //<img localsrc="334" />
    //        $regex = '/(<img [^>]*localsrc\s*=\s*["\']?)([^>"\']+)(["\']?[^>]*>)/is';
    //        $string = preg_replace_callback($regex, array(__CLASS__, '_onConvertLocalSrcEmoji'), $string);
    //        return $string;
    //    }

    /**
     * 絵文字を除去する
     *
     * @param string $string 文字列
     *
     * @return string 文字列
     */
    public function removeEmoji($string)
    {
        switch ($this->_ua) {
            // Docomo, Au
            case BEAR_Agent::UA_DOCOMO:
            case BEAR_Agent::UA_EZWEB:
                /* @var $emoji BEAR_Emoji */
                $decEntity = $this->makeDecEntity($string);
                $regex = '/&#(\d{5});/is';
                $string = preg_replace($regex, '', $decEntity);
                break;
            // SBモバイル
            case BEAR_Agent::UA_SOFTBANK:
                $regex = '/\x1b\x24[GEFOPQ][\x21-\x7a]*\x0f/is';
                $string = preg_replace($regex, '', $string);
                break;
            default:
                break;
        }

        return $string;
    }

    /**
     * エスケープされた文字列の解除
     *
     * <pre>QuciFormバリデーションNGの場合valuesに入った
     * 文字列がエスケープされます。
     *
     * SBの絵文字では"（ダブルクオーテーション）などを使用したものがあり、
     * 誤動作してしまいます。
     * この関数をsamrtyのoutputfilterで使用して誤表示を防ぎます。
     *　HTMLとしては誤った表記になるのでPCでの表示はうまくできませんが
     * SB端末では正しく表示されます</pre>
     *
     * @param string $html HTML
     *
     * @return string
     */
    public function unescapeSbEmoji($html)
    {
        $regex = '/\x1b\x24(.*?)\x0f/is';
        $result = preg_replace_callback($regex, array(__CLASS__, '_onSbEmoji'), $html);

        return $result;
    }

    /**
     * 絵文字を全て除去する
     *
     * QuickFormのフィルターなどに使います。
     *
     * @param string $string 文字列
     *
     * @return string
     */
    public static function removeEmojiEntity($string)
    {
        // iモード絵文字消去
        // EZ絵文字消去
        // SB絵文字消去
        $regex = '/(&#(\d{5});)|(\x1b\x24[GEFOPQ][\x21-\x7a]*\x0f)/is';
        $string = preg_replace($regex, '', $string);

        return $string;
    }

    /**
     * 絵文字をエンティティに変換
     *
     * @param string     &$string
     * @param array      $keys
     * @param BEAR_Emoji $emoji
     */
    public static function onEntityEmoji(
        &$string,
        /* @noinspection PhpUnusedParameterInspection */
        $keys,
        $emoji
    ) {
        $string = $emoji->makeDecEntity($string);
    }

    /**
     * エージェント別固定絵文字の取得
     *
     * @param string $emoji SUNなどの絵文字単語
     *
     * @return string
     */
    public function getAgentEmoji($emoji)
    {
        static $emojiChars = array();

        if (! $emojiChars) {
            $ua = BEAR::dependency('BEAR_Agent')->getUa();
            $file = _BEAR_BEAR_HOME . "/BEAR/Emoji/Conf/{$ua}.php";
            if (file_exists($file)) {
                /** @noinspection PhpIncludeInspection */
                include $file;
            } else {
                include _BEAR_BEAR_HOME . '/BEAR/Emoji/Conf/Default.php';
            }
        }

        return $emojiChars[$emoji];
    }

    /**
     * 正規表現により絵文字を数値エンティティに変換
     *
     * makeDecEntity()からコールされます。
     *
     * @param string $string 文字列
     * @param string $emoji  絵文字の正規表現
     *
     * @return string
     */
    private function _makeEntityBySjisRegex($string, $emoji)
    {
        $mbRegexEncoding = mb_regex_encoding();
        mb_regex_encoding('SJIS');
        $sjis = '[\x81-\x9F\xE0-\xEF][\x40-\x7E\x80-\xFC]|[\x00-\x7F]|[\xA1-\xDF]';
        $pattern = "/\G((?:$sjis)*)(?:($emoji))/";
        // 絵文字を検索
        preg_match_all($pattern, $string, $arr); // $arr[2]に対象絵文字が格納される
        // 絵文字を置換
        $converted = $string;
        foreach ($arr[2] as $value) {
            $patternRep = "$value";
            $emojiCd = unpack('C*', $value);
            $hex = dechex($emojiCd[1]) . dechex($emojiCd[2]);
            $replacement = '&#' . hexdec($hex) . ';';
            $converted = mb_ereg_replace($patternRep, $replacement, $converted);
        }
        mb_regex_encoding($mbRegexEncoding);

        return $converted;
    }

    /**
     * 16進エンティティ絵文字表記にするための正規表現からコールバックメソッド
     *
     * @param array $matches 検索文字列
     *
     * @return string
     */

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function _onHexEntity($matches)
    {
        $result = '&#x' . dechex($matches[1]) . ';';

        return $result;
    }

    /**
     * imageTag用コールバック関数
     *
     * <pre>自分のキャリアではない絵文字をイメージタグに変換して返します</pre>
     *
     * @param array $matches 検索文字列
     *
     * @return string
     *
     * @deprecated
     */

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function _onEmojiImage($matches)
    {
        $emojiId = $matches[1];
        // キャリア判定
        if (self::DOCOMO_MIN <= $emojiId && $emojiId <= self::DOCOMO_MAX) {
            $emojiUa = BEAR_Agent::UA_DOCOMO;
        } elseif (self::EZWEB_MIN <= $emojiId && $emojiId <= self::EZWEB_MAX) {
            $emojiUa = BEAR_Agent::UA_EZWEB;
        } elseif (self::SOFTBANK_MIN <= $emojiId && $emojiId <= self::SOFTBANK_MAX) {
            $emojiUa = BEAR_Agent::UA_SOFTBANK;
        } else {
            $emojiUa = false;
        }
        $ua = self::$_staticConfig['ua'];
        if ($ua !== $emojiUa && $emojiUa !== false) {
            $emojiPath = isset(self::$_staticConfig['emoji_path']) ? self::$_staticConfig['emoji_path'] : '/emoji';
            $emojiFontSize = ($ua === BEAR_Agent::UA_DEFAULT && isset(self::$_staticConfig['emoji_path']) && isset(self::$_staticConfig['pc_emoji_size']) && self::$_staticConfig['pc_emoji_size'] !== 20) ? '12' : '20';
            $result = '<img src="' . $emojiPath . '/' . strtolower($emojiUa) . '/' . $emojiFontSize . '/' . dechex(
                $emojiId
            ) . '.gif" class="bear_emoji" border="0" />';
        } else {
            // 絵文字ではないエンティティ
            $result = "&#{$emojiId};";
        }

        return $result;
    }

    /**
     * 絵文字コードマップの取得
     *
     * 絵文字コードマップの配列を取得します。
     *
     * マップフォーマット
     * i,e
     * 絵文字UNICODE開始, 絵文字UNICODE終了,UNICODE<=>SJISオフセット, マスク
     *
     * @param mixed $ua ユーザーエージェントレター
     *
     * @return array
     */
    private function _getEmojiMap($ua = false)
    {
        if ($ua === false) {
            $ua = $this->_ua;
        }
        switch ($ua) {
            case BEAR_Agent::UA_DOCOMO:
                $result = array(
                    0xE63E,
                    0xE69B,
                    0x1261,
                    0xFFFF,
                    0xE69C,
                    0xE6A5,
                    0x12A4,
                    0xFFFF,
                    0xE6CE,
                    0xE6DA,
                    0x12A4,
                    0xFFFF,
                    0xE6DB,
                    0xE757,
                    0x12A5,
                    0xFFFF
                );
                break;
            case BEAR_Agent::UA_EZWEB:
                $result = array(
                    0xE468,
                    0xE4A6,
                    0x11D8,
                    0xFFFF,
                    0xE4A7,
                    0xE523,
                    0x11D9,
                    0xFFFF,
                    0xE524,
                    0xE562,
                    0x121C,
                    0xFFFF,
                    0xE563,
                    0xE5B4,
                    0x121D,
                    0xFFFF,
                    0xE5B5,
                    0xE5CC,
                    0x1230,
                    0xFFFF,
                    0xE5CD,
                    0xE5DF,
                    0x0D73,
                    0xFFFF,
                    0xEA80,
                    0xEAAB,
                    0x08D3,
                    0xFFFF,
                    0xEAAC,
                    0xEAFA,
                    0x08D4,
                    0xFFFF,
                    0xEAFB,
                    0xEB0D,
                    0x0CD7,
                    0xFFFF,
                    0xEB0E,
                    0xEB3B,
                    0x08C1,
                    0xFFFF,
                    0xEB3C,
                    0xEB7A,
                    0x0904,
                    0xFFFF,
                    0xEB7B,
                    0xEB88,
                    0x0905,
                    0xFFFF
                );
                break;
            case BEAR_Agent::UA_SOFTBANK:
                $result = array(
                    0xE001,
                    0xE05A,
                    0,
                    0xFFFF,
                    0xE101,
                    0xE15A,
                    0,
                    0xFFFF,
                    0xE201,
                    0xE25A,
                    0,
                    0xFFFF,
                    0xE301,
                    0xE34D,
                    0,
                    0xFFFF,
                    0xE401,
                    0xE44C,
                    0,
                    0xFFFF,
                    0xE501,
                    0xE53E,
                    0,
                    0xFFF
                );
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * 絵文字変換コールバック
     *
     * @param array $matches 検索文字列
     *
     * @return string
     */

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function _onConvertEmoji($matches = false)
    {
        static $convertMap;
        //変換マップ読み込み
        if (! is_array($convertMap)) {
            $convertMap = BEAR_Emoji_Map::getEmojiConvertArray();
        }
        $to = &PEAR::getStaticProperty(__CLASS__, 'to');
        //SB携帯連続絵文字分解
        //
        //最初の文字が1Bで6文字以上の文字列を連続絵文字とする
        //
        if (substr($matches[0], 0, 1) == chr((0x1b)) && (strlen($matches[0]) > 5)) {
            $stirng = $matches[0];
            $sbEmoji = array();
            $max = strlen($stirng) - 1;
            for ($i = 3; $i < $max; $i++) {
                $sbEmoji[] = $stirng[$i];
            }
            $emojis = array();
            $j = 0;
            foreach ($sbEmoji as $emoji) {
                $webcodeDecimalHigh = ord($stirng[2]);
                $webcodeDecimalLow = ord($emoji);
                //1B24 (ESC開き)
                $emojis[$j] = pack('C*', 0x1b);
                $emojis[$j] .= pack('C*', 0x24);
                $emojis[$j] .= pack('C*', $webcodeDecimalHigh);
                $emojis[$j] .= pack('C*', $webcodeDecimalLow);
                //0F (ESC閉じ)
                $emojis[$j] .= pack('C*', 0x0f);
                $j++;
            }
            //コードの数値配列を文字に
            $result = $emoji = '';
            foreach ($emojis as $emoji) {
                $converted = $convertMap[$emoji][$to];
                $result .= ($converted) ? $converted : '';
            }

            return $result;
        }
        $emoji = $matches[0];
        $converted = $convertMap[$emoji][$to];
        // docomo推奨バイナリ絵文字
        if (($to == BEAR_Agent::UA_DOCOMO || $to == BEAR_Agent::UA_EZWEB) && preg_match('/&#(\d{5});/', $converted)) {
            $decCode = (int) preg_replace('/&#(\d{5});/', '$1', $converted);
            $len = strlen($decCode);
            if ($len > 5) {
                $result = '';
                for ($i = 0; $i < $len / 5; $i++) {
                    $result .= pack('n', substr($decCode, $i * 5, 5));
                }
            } else {
                $result = pack('n', $decCode);
            }
        } else {
            //            $encode =& PEAR::getStaticProperty(__CLASS__, 'encode');
            $encode = mb_detect_encoding($converted, mb_detect_order(), true);
            $converted = mb_convert_kana($converted, 'ak', $encode);
            $converted = str_replace(' ', '', $converted);
            $result = ($converted) ? $converted : '';
        }

        return $result;
    }

    /**
     * unescapesbEmoji用コールバック関数
     *
     * @param array $match 検索文字列
     *
     * @return string
     */

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function _onSbEmoji($match)
    {
        $emoji = $match[1];
        $result = pack('c*', 0x1b, 0x24) . html_entity_decode($emoji) . pack('c*', 0x0f);

        return $result;
    }
}
