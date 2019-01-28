<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Dom query
 *
 * Zend_Dom_QueryにgetXmlメソッドを追加
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
        $xml = [];
        foreach ($results as $result) {
            $xml[] = $results->getDocument()->saveXML($result);
        }

        return $xml;
    }
}
