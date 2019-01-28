<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * BEAR_Resource_Request for for unit testing
 */
class BEAR_Resource_Request_Test extends BEAR_Resource_Request
{
    /**
     * Resource request
     *
     * @return BEAR_Ro
     *
     * @see BEAR_Resource_Request::request()
     * @throws Exception
     */
    public function request()
    {
        $query = http_build_query($this->_config['values']);
        if ($query) {
            $query = '?' . $query;
        }
        $resurceQuery = $this->_config['method'] . ' ' . $this->_config['uri'] . (string) $query;
        BEAR::dependency('BEAR_Log')->log('resource', $resurceQuery);
        $result = parent::request();

        return $result;
    }
}
