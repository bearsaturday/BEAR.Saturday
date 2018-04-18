<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * XML出力
 *
 * XMLを出力します。
 *
 * @param array $values  値
 * @param array $options オプション
 *
 * @return BEAR_Ro
 */
function outputXml($values, array $options)
{
    $defaultOptions = array(
        'mode' => '',
        'indent' => '    ',
        'linebreak' => "\n",
        'indentAttributes' => true,
        'typeHints' => false,
        'scalarAsAttributes' => true,
        'addDecl' => true,
        'encoding' => 'UTF-8',
        'rootName' => 'rdf:RDF',
        'rootAttributes' => array(
            'xmlns' => 'http://purl.org/rss/1.0/',
            'xmlns:rdf' => 'http://www.e3.org/1999/02/22-rdf-syntax-ns#',
            'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
            'xmlns:sy' => 'http://purl.org/rss/1.0/modules/syndication/',
            'xmlns:admin' => 'http://webns.net/mvcb/',
            'xmlns:content' => 'http://purl.org/rss/1.0/modules/content/',
            'xml:lang' => 'ja'
        ),
        'defaultTagName' => 'item',
        'attributesArray' => '_attributes'
    );
    if (is_array($options)) {
        $options = $options + $defaultOptions;
    } else {
        $options = $defaultOptions;
    }
    $serializer = new XML_Serializer($options);
    $status = $serializer->serialize($values);
    // エラーチェック
    if (PEAR::isError($status)) {
        $xmlString = '';
    } else {
        $xmlString = $serializer->getSerializedData();
    }
    //　return
    $body = $xmlString;
    $headers = array('X-BEAR-Output: XML' => 'Content-Type: application/xml');
    $ro = BEAR::factory('BEAR_Ro');
    $ro->setBody($body);
    $ro->setHeaders($headers);

    return $ro;
}
