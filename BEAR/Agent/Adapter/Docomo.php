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
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       https://github.com/bearsaturday
 */

/**
 * Docomoエージェントアダプター
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
 */
class BEAR_Agent_Adapter_Docomo extends BEAR_Agent_Adapter_Mobile implements BEAR_Agent_Adapter_Interface
{
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $contentType = isset($this->_config['content_type']) ? $this->_config['content_type'] : 'application/xhtml+xml';
        $this->_config['agent_filter'] = true;
        $this->_config['input_encode'] = 'SJIS-win';
        $this->_config['output_encode'] = 'SJIS-win';
        $this->_config['header'] = 'Content-Type: ' . $contentType . '; charset=Shift_JIS';
        $this->_config['charset'] = 'Shift_JIS';
        $this->_config['session_trans_sid'] = true;
        $this->_config['enable_js'] = false;
        $this->_config['enable_css'] = true;
        $this->_config['enable_inline_css'] = false;
        /** @todo inline CSS */
        $this->_config['role'] = array(BEAR_Agent::UA_DOCOMO, BEAR_Agent::UA_MOBILE, BEAR_Agent::UA_DEFAULT);
    }
}
