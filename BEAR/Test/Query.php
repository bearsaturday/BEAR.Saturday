<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * Dom query
 *
 * Zend_Dom_QueryにgetXmlメソッドを追加
 *
 * @license    @license@ http://@license_url@
 *
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
