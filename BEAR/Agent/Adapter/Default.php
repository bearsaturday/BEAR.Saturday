<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * Defaultエージェントアダプター
 */
class BEAR_Agent_Adapter_Default extends BEAR_Agent_Adapter implements BEAR_Agent_Adapter_Interface
{
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['role'] = array(BEAR_Agent::UA_DEFAULT);
        $this->_config['agent_filter'] = true;
        $this->_config['charset'] = 'utf-8';
        $this->_config['enable_js'] = true;
        $this->_config['enable_inline_css'] = false;
        $this->_config['enable_css'] = true;
        $this->_config['enable_session'] = true;
        $this->_config['session_trans_sid'] = false;
    }
}
