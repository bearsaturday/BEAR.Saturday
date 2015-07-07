<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 */

/**
 * Mobileエージェントアダプター
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 */
abstract class BEAR_Agent_Adapter_Mobile extends BEAR_Agent_Adapter_Default
{
    /**
     * @var string
     */
    protected $_header;

    /**
     * @var Smarty
     */
    protected $_smarty;

    /**
     * 携帯サ絵文字ポート対応なし
     *
     * @var integer
     */
    const SUPPORT_NONE = 0;

    /**
     * 携帯絵文字サポートIMG変換
     *
     * @var integer
     */
    const SUPPORT_IMG = 1;

    /**
     * 携帯絵文字サポートIMG変換
     *
     * @var integer
     */
    const SUPPORT_CONV = 2;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $contentType = isset($this->_config['content_type']) ? $this->_config['content_type'] : 'application/xhtml+xml';
        $this->_config['is_mobile'] = true;
        $this->_config['agent_filter'] = true;
        $this->_config['header'] = 'Content-Type: ' . $contentType . '; charset=Shift_JIS';
        $this->_config['charset'] = 'utf-8';
        $this->_config['enable_js'] = false;
        $this->_config['role'] = array(BEAR_Agent::UA_MOBILE, BEAR_Agent::UA_DEFAULT);
    }

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $this->_header = BEAR::dependency('BEAR_Page_Header');
        $this->_smarty = BEAR::dependency('BEAR_Smarty', array('ua' => $this->_config['ua']));
    }

    /**
     * UTF-8化コールバック関数
     *
     * @param string &$value 文字列
     *
     * @return void
     */
    public static function onUTF8(&$value)
    {
        BEAR::dependency(__CLASS__)->onUTF8($value);
    }

    /**
     * UTF-8化
     *
     * @param string &$value 文字列
     *
     * @throws BEAR_Exception
     * @return void
     * @ignore
     */
    public function UTF8(&$value)
    {
        if (!mb_check_encoding($value, $this->_codeFromMoble)) {
            $msg = 'Illegal Submit Values';
            $info = array('value' => $value);
            throw $this->_exception(
                $msg,
                array(
                    'code' => BEAR::CODE_BAD_REQUEST,
                    'info' => $info
                )
            );
        }
        $value = mb_convert_encoding($value, 'utf-8', $this->_codeFromMoble);
        if (!mb_check_encoding($value, 'utf-8')) {
            $msg = 'Illegal UTF-8';
            $info = array('value' => $value);
            throw $this->_exception(
                $msg,
                array(
                    'code' => BEAR::CODE_BAD_REQUEST,
                    'info' => $info
                )
            );
        }
    }
}
