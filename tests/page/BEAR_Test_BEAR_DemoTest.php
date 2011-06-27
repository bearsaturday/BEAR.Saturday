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
     * リソース +リンク +テンプレート
     *
     */
    public function testResourceTemplateLink()
    {
        $params = array(
            'uri' => 'page://self/resource/link',
            'values' => array('id' => 1),
            'options' => array(
                'output' => 'html'
            )
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content ul li');
        $expected = '<li>Athos</li>';
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#blog ul ul li.trackback');
        $expected = '<li class="trackback">記事ID(101)のトラックバック155</li>';
        $this->assertSame($expected, $xml[5]);
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content p');
        $expected = '<p>[Athos]のユーザーリソースは問題なく取得できました</p>';
        $this->assertSame($expected, $xml[0]);
    }

    /**
     * リソース +リンク +テンプレート + ページャー
     *
     */
    public function testResourceTemplateLinkPage1()
    {
        $params = array(
            'uri' => 'page://self/resource/link/pager',
            'values' => array('id' => 2),
            'options' => array(
                'page' => 1,
                'output' => 'html'
            )
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#blog ul li.entry span.title');
        $expected = '<span class="title">PHP</span>';
        $this->assertSame($expected, $xml[0]);
    }

    /**
     * リソース +リンク +テンプレート + ページャー
     *
     */
    public function testResourceTemplateLinkPage2()
    {
        $params = array(
            'uri' => 'page://self/resource/link/pager',
            'values' => array('id' => 2),
            'options' => array(
                'page' => 2,
                'output' => 'html'
            )
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#blog ul li.entry span.title');
        $expected = '<span class="title">Go</span>';
        $this->assertSame($expected, $xml[0]);
    }

    /**
     * HTTPリソース
     *
     */
    public function testResourceHttp()
    {
        $params = array(
            'uri' => 'page://self/resource/html/index',
            'values' => array('host' =>'bear.demo',
                              'uri' => 'http://www.feedforall.com/sample.xml'),
            'options' => array(
                'output' => 'html'
            )
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content ul li a');
        $expected = '<a href="http://www.feedforall.com/weather.htm">RSS Solutions for Meteorologists</a>';
        $this->assertSame($expected, $xml[0]);
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#remote_string');
        $expected = '<div id="remote_string">html=[Hello BEAR.]</div>';
        $this->assertSame($expected, $xml[0]);
    }

    /**
     * Docomo出力テスト
     */
    public function testPageResourceDocomo()
    {
        $params = array(
            'uri' => 'page://self/db/select/item',
            'values' => array('id' => 1),
            'options' => array(
                'output' => 'html',
                'page' => array('ua' => 'Docomo')
            )
        );
        $ro = $this->_resource->read($params)->getRo();
        $actual = $ro->getHeaders();
        $expectedDocomoHeaders = 'Content-Type: text/html; charset=Shift_JIS';
        $this->assertSame($expectedDocomoHeaders, $actual[0]);
        $body = $ro->getBody();
        $encode = mb_detect_encoding($body, "JIS, eucjp-win, sjis-win");
        $this->assertSame('SJIS-win', $encode);
    }

    /**
     * Pageリソースのページのリソース
     *
     */
    public function testResourcePage()
    {
        $params = array(
            'uri' => 'page://self/resource/page',
            'values' => array(),
            'options' => array(
                'output' => 'resource'
            )
        );
        $body = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($body['body'], 'html#beardemo body div.content h2');
        $expected = '<h2>PHP</h2>';
        $this->assertSame($expected, $xml[0]);
    }



    /**
     * セットオプション
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

    /**
     * CSVリソース
     */
    public function testResourceCsv()
    {
        $params = array(
            'uri' => 'page://self/resource/csv',
            'values' => array(),
            'options' => array(
                'output' => 'html'
            )
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content ul li');
        $expected = 25;
        $this->assertSame($expected, count($xml));
        $expected = '<li>
      〒13101-102  　東京都千代田区飯田橋
    </li>';
        $actual = $xml[0];
        $this->assertSame($expected, $actual);
        $expected = '<li>
      〒13101-100  　東京都千代田区霞が関霞が関ビル（１６階）
    </li>';
        $actual = $xml[24];
        $this->assertSame($expected, $actual);
    }

    /**
     * AOPテスト
     *
     * test/aop.php
     */
    public function testTestAop()
    {
        $values = array('id' => 1);
        // オリジナル
        $params = array('uri' => 'Test/Aop', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $expected = array (
          'id' => 1,
          'name' => 'BEAR',
        );
        $this->assertSame($expected, $actualBody);
        // before アドバイス
        $params = array('uri' => 'Test/Aop/Before', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $expected = array (
          'id' => 2,
          'name' => 'Kuma',
        );
        $this->assertSame($expected, $actualBody);
        // after アドバイス
        $params = array('uri' => 'Test/Aop/After', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $expected = array (
          'id' => 1,
          'name' => 'BEAR',
          'age' => 10,
        );
        $this->assertSame($expected, $actualBody);
        // around アドバイス
        $params = array('uri' => 'Test/Aop/Around', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $this->assertTrue($actualBody['id'] === 1);
        $this->assertTrue($actualBody['name'] === 'BEAR');
        $this->assertTrue(isset($actualBody['sec']));
        $this->assertInternalType('float', $actualBody['sec']);

        // returning アドバイス
        $params = array('uri' => 'Test/Aop/Returning', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $expected = array (
          'id' => 1,
          'name' => 'BEAR',
          'is_no_problem' => true,
        );
        $this->assertSame($expected, $actualBody);
        // throwing アドバイス (1)
        $params = array('uri' => 'Test/Aop/Throwing', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $expected = array (
          'is_error' => true,
        );
        $this->assertSame($expected, $actualBody);

        // throwing アドバイス (2)
        $params = array('uri' => 'Test/Aop/Throwing2', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $expected = array (
          'is_error' => true,
        );
        $this->assertSame($expected, $actualBody);

        // 上記全てのアドバイスを適用したAll
        $params = array('uri' => 'Test/Aop/All', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $this->assertTrue($actualBody['id'] === 2);
        $this->assertTrue($actualBody['name'] === 'Kuma');
        $this->assertTrue(isset($actualBody['sec']));
        $this->assertInternalType('float', $actualBody['sec']);
        $this->assertTrue($actualBody['age'] === 10);
        $this->assertTrue($actualBody['is_no_problem'] === true);

        // 同じアドバイスの流用
        $values = array('id' => 3);
        $params = array('uri' => 'Test/Aop/Around2', 'values' => $values);
        $actualBody = $this->_resource->read($params)->getBody();
        $this->assertTrue($actualBody['id'] === 3);
        $this->assertTrue($actualBody['name'] === 'クマ');
        $this->assertTrue(isset($actualBody['sec']));
        $this->assertInternalType('float', $actualBody['sec']);
    }
}
