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
 * Defaultエージェントアダプター
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
