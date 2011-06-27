<?php
/**
 * BEAR
 *
 * @category   BEAR
 * @package    Test
 * @subpackage resource
 */

$bearMode = 0;
require __DIR__ . '/../sites/bear.demo/App.php';

/**
 * @category   BEAR
 * @package    Test
 * @subpackage resource
 */
class BEAR_Test_BEAR_DemoTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        restore_error_handler();
        error_reporting(E_ALL);
        restore_exception_handler();
        $this->_resource = new BEAR_Resource(array());
        $this->_resource->onInject();
        $this->_query = new BEAR_Test_Query;
    }

    /**
     * リソーステンプレート
     *
     * page://self/resource/template
     */
    public function testResourceTemplate()
    {
        $params = array(
            'uri' => 'page://self/resource/template',
            'values' => array('id' => 1),
            'options' => array(
                'output' => 'html'
            )
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#user_resource ul li');
        $expected = '<li>Athos</li>';
        $this->assertSame($expected, $xml[0]);
        $expected = '<li>15</li>';
        $this->assertSame($expected, $xml[1]);
        $expected = '<li>ID=1</li>';
        $this->assertSame($expected, $xml[2]);
    }

    /**
     * リソーステンプレート
     *
     * page://self/resource/template
     */
    public function testResourceSetIndex()
    {
        $params = array(
            'uri' => 'page://self/resource/set/index',
            'values' => array(),
            'options' => array(
                'output' => 'html'
            )
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content ul.entry li a.entry-title');
        $expected = '<a href="/db/select/item.php?id=1" class="entry-title">PHP</a>';
        $this->assertSame($expected, $xml[0]);
        $this->assertSame($expected, $xml[5]);
        $this->assertSame($expected, $xml[10]);
        $this->assertSame($expected, $xml[15]);
        $this->assertSame($expected, $xml[20]);
        $this->assertSame($expected, $xml[25]);
    }
}
