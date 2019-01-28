<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * アブストラクトエージェントアダプター
 *
 * @config bool   enable_js         JS可？
 * @config bool   enable_css        CSS可？
 * @config bool   enable_inline_css DocomoのCSS用にtoInlineCSSDoCoMo使用？
 * @config string role              ロール
 * @config array  header            HTTPヘッダー
 * @config bool   agent_filter      フィルター処理?
 * @config string output_encode     出力時の文字コード
 */
abstract class BEAR_Agent_Adapter extends BEAR_Base
{
    /**
     * 携帯絵文字サブミット無変換
     *
     * @var int
     */
    const MOBILE_SUBMIT_PASS = 0;

    /**
     * 携帯絵文字サブミット無変換
     *
     * @var int
     */
    const MOBILE_SUBMIT_ENTITY = 1;

    /**
     * 携帯絵文字サブミット除去
     *
     * @var int
     */
    const MOBILE_SUBMIT_REMOVE_EMOJI = 2;

    /**
     * サブミット処理
     *
     * エージェントの設定に応じてサブミットされた$_POSTまたは$_GETの文字列を
     * 絵文字エンティティや文字コード変換をします。
     *
     * @internal グローバル変数を直接変更してるのはそれに依存してるHTML_Quickformなどのライブラリのためです
     */
    public function submit()
    {
        static $done = false;

        $hasSubmit = (isset($_POST['_token']) || isset($_GET['_token'])) ? true : false;
        if (! $hasSubmit && $done) {
            return;
        }
        if (isset($_POST['_token'])) {
            $input = &$_POST;
        } elseif (isset($_GET['_token'])) {
            $input = &$_GET;
        } else {
            return;
        }
        $done = true;
        $app = BEAR::get('app');
        $emojiSubmit = isset($app['BEAR_Emoji']['submit']) ? $app['BEAR_Emoji']['submit'] : 'pass';
        switch ($emojiSubmit) {
            // 何もしない
            case 'pass':
                break;
            case 'entity':
                // 絵文字をエンティティに変換
                // 3GCsエンコード変換必須
                if ($this->_config['is_mobile']) {
                    array_walk_recursive($input, ['BEAR_Emoji', 'onEntityEmoji'], BEAR::dependency('BEAR_Emoji'));
                }

                break;
            // 絵文字除去
            case 'remove':
                array_walk_recursive($input, ['BEAR_Emoji', 'removeEmoji'], BEAR::dependency('BEAR_Emoji'));

                break;
            default:
                trigger_error('Illegal $this->_config[\'agent\'] error', E_USER_WARNING);

                break;
        }
        // UTF8に文字コード変換
        if (isset($this->_config['input_encode'])) {
            array_walk_recursive($input, [__CLASS__, 'onUTF8'], $this->_config['input_encode']);
        }
    }

    /**
     * UTF-8化コールバック関数
     *
     * <pre>
     * $this->_config['input_encode']からUTF-8に変換します。
     * mb_check_encoding()関数でコードが適切が判断され問題があると例外が投げられます
     * </pre>
     *
     * @param string &$value      文字列
     * @param string $key         キー
     * @param string $inputEncode エンコード
     *
     * @throws BEAR_Agent_Exception
     */
    public static function onUTF8(
        &$value,
        /* @noinspection PhpUnusedParameterInspection */
        $key,
        $inputEncode
    ) {
        if (! mb_check_encoding($value, $inputEncode)) {
            $msg = 'Illegal Submit Values';
            $info = ['value' => $value];

            throw new BEAR_Agent_Exception($msg, [
                'code' => BEAR::CODE_BAD_REQUEST,
                'info' => $info
            ]);
        }
        $value = mb_convert_encoding($value, 'utf-8', $inputEncode);
        if (! mb_check_encoding($value, 'utf-8')) {
            $msg = 'Illegal UTF-8';
            $info = ['value' => $value];

            throw new BEAR_Agent_Exception($msg, [
                'code' => BEAR::CODE_BAD_REQUEST,
                'info' => $info
            ]);
        }
    }
}
