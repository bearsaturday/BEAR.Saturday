<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Test
 * @subpackage DOM
 * @subpackage Client
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net/
 */

/**
 * Dom query
 *
 * Zend_Dom_QueryにgetXmlメソッドを追加
 *
 * @category   BEAR
 * @package    BEAR_Test
 * @subpackage DOM
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 *
 * @Singleton
 */
class BEAR_Test_Query extends Zend_Dom_Query
{
    /**
     * Get xml
     *
     * @param string $html
     * @param string $selecter
     *
     * @return string
     */
    public function getXml($html, $selecter)
    {
        $this->setDocumentHtml($html);
        $results = $this->query($selecter);
        $xml = array();
        foreach ($results as $result) {
            $xml[] = $results->getDocument()->saveXML($result);
        }
        return $xml;
    }
}