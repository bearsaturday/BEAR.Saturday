<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Docomoエージェントアダプター
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
        /* @todo inline CSS */
        $this->_config['role'] = array(BEAR_Agent::UA_DOCOMO, BEAR_Agent::UA_MOBILE, BEAR_Agent::UA_DEFAULT);
    }
}
