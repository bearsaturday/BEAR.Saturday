<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Defaultエージェントアダプター
 */
class BEAR_Agent_Adapter_Default extends BEAR_Agent_Adapter implements BEAR_Agent_Adapter_Interface
{
    /**
     * Constructor
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['role'] = [BEAR_Agent::UA_DEFAULT];
        $this->_config['agent_filter'] = true;
        $this->_config['charset'] = 'utf-8';
        $this->_config['enable_js'] = true;
        $this->_config['enable_inline_css'] = false;
        $this->_config['enable_css'] = true;
        $this->_config['enable_session'] = true;
        $this->_config['session_trans_sid'] = false;
    }
}
