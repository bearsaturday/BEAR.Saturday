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
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
 */
/**
 * エージェントアダプタークラス
 *
 * <pre>
 * エージェントアダプター抽象クラスです。BEAR/Agent/Adapter/の各クラスで実装します。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
 * @abstract
 *  */
abstract class BEAR_Agent_Adaptor extends BEAR_Base
{
    /**
     * 携帯絵文字サブミット無変換
     *
     * @var integer
     */
    const MOBILE_SUBMIT_PASS = 0;

    /**
     * 携帯絵文字サブミット無変換
     *
     *  @var integer
     */
    const MOBILE_SUBMIT_ENTITY = 1;

    /**
     * 携帯絵文字サブミット除去
     *
     *  @var integer
     */
    const MOBILE_SUBMIT_REMOVE_EMOJI = 2;

    /**
     * コンストラクタ.
     *
     * @param array $options ユーザーコンフィグ
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * サブミット処理
     *
     * <pre>
     * エージェントの設定に応じて絵文字エンティティや文字コード変換をします。
     * </pre>
     *
     * @param string $input 入力文字列
     *
     * @return string
     */
    public function submit()
    {
    	static $done = false;

    	$hasSubmit = (isset($_POST['_token']) || isset($_GET['_token'])) ? true : false;
        if (!$hasSubmit && $done) {
            return;
        }
        if (isset($_POST['_token'])) {
        	$input =& $_POST;
        } elseif (isset($_GET['_token'])) {
        	$input =& $_GET;
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
            // 絵文字をエンティティに変換
            case 'entity':
                //3GCsエンコード変換必須
                if ($this->_config['is_mobile']) {
                    array_walk_recursive($input, array('BEAR_Emoji', 'onEntityEmoji'), BEAR::dependency('BEAR_Emoji'));
                }
                break;
            // 絵文字除去
            case 'remove':
                array_walk_recursive($input, array('BEAR_Emoji', 'removeEmoji'), BEAR::dependency('BEAR_Emoji'));
                break;
            default :
                trigger_error('Illigal $this->_config[\'agent\'] error', E_USER_WARNING);
            break;
        }
        // UTF8に文字コード変換
        if (isset($this->_config['input_encode'])) {
            array_walk_recursive($input, array(__CLASS__, 'onUTF8'), $this->_config['input_encode']);
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
     *
     * @param string &$value 文字列
     *
     * @return void
     * @ignore
     */
    public static function onUTF8(&$value, $key, $inputEncode)
    {
        if (!mb_check_encoding($value, $inputEncode)) {
            $msg = 'Illigal Submit Values';
            $info = array('value' => $value);
            throw new BEAR_Exception($msg, array(
                'code' => BEAR::CODE_BAD_REQUEST,
                'info' => $info));
        }
        $value = mb_convert_encoding($value, 'utf-8', $inputEncode);
        if (!mb_check_encoding($value, 'utf-8')) {
            $msg = 'Illigal UTF-8';
            $info = array('value' => $value);
            throw new BEAR_Exception($msg, array(
                'code' => BEAR::CODE_BAD_REQUEST,
                'info' => $info));
        }
    }
}