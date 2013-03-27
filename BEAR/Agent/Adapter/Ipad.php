<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Adapter
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net/
 */

/**
 * Ipadエージェントアダプター
 *
 * @category   BEAR
 * @package    BEAR_Agent
 * @subpackage Adapter
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
 */
class BEAR_Agent_Adapter_Ipad extends BEAR_Agent_Adapter implements BEAR_Agent_Adapter_Interface
{
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['agent_filter'] = true;
        $contentType = isset($this->_config['content_type']) ? $this->_config['content_type'] : 'text/html';
        $this->_config['header'] = 'Content-Type: ' . $contentType . '; charset=utf-8';
        $this->_config['charset'] = 'utf-8';
        $this->_config['enable_js'] = true;
        $this->_config['role'] = array(BEAR_Agent::UA_IPAD, BEAR_Agent::UA_DEFAULT);
    }
}
