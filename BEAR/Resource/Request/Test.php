<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Request
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * BEAR_Resource_Request for for unit testing
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Request
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Request_Test extends BEAR_Resource_Request
{
    /**
     * Resource request
     *
     * @return BEAR_Ro
     *
     * @see BEAR_Resource_Request::request()
     */
    public function request()
    {
        $query = http_build_query($this->_config['values']);
        if ($query) {
            $query = '?' . $query;
        }
        $resurceQuery = $this->_config['method'] . ' ' . $this->_config['uri'] . (string)$query;
        BEAR::dependency('BEAR_Log')->log('resource', $resurceQuery);
        $result = parent::request();
        return $result;
    }
}