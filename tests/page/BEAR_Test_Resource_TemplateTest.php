<?php
/**
 * BEAR
 *
 * @category   BEAR
 * @package    Test
 * @subpackage resource
 */


/**
 * @category   BEAR
 * @package    Test
 * @subpackage resource
 */
class BEAR_resources_Test extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $bearMode = 0;
        $basePath = dirname(dirname(__DIR__));
        $bearDemoPath = $basePath . '/vendor/bearsaturday/demo.local';
        set_include_path($basePath . PATH_SEPARATOR . $bearDemoPath . PATH_SEPARATOR . get_include_path());
        require $bearDemoPath . '/App.php';
        restore_error_handler();
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        restore_exception_handler();
        $this->_resource = new BEAR_Resource(array());
        $this->_resource->onInject();
        $this->_query = new BEAR_Test_Query;
    }

    /**
     * リソーステンプレートを適用しリソースを持つページのテスト
     * @runInSeparateProcess
     */
    public function testResourceWithTemplate()
    {
        $params = array(
            'uri' => 'page://self/db/select/index',
            'values' => array('id' => 1),
            'options' => array('output' => 'html')
        );
        $html = $this->_resource->read($params)->getBody();
        $this->_query->setDocumentHtml($html);
        $results = $this->_query->query('html#beardemo body div.content ul.entry li a.entry-title');
        $expected = 5;
        $actual = count($results);
        $this->assertSame($expected, $actual);
        $results = $this->_query->query('html#beardemo body div.content ul.entry li a.entry-title');
        $xml = array();
        foreach ($results as $result) {
            $xml[] = $results->getDocument()->saveXML($result);
        }
        $expected = '<a href="/db/select/item.php?id=1" class="entry-title">PHP</a>';
        $this->assertSame($expected, $xml[0]);
        $expected = '<a href="/db/select/item.php?id=2" class="entry-title">Java</a>';
        $this->assertSame($expected, $xml[1]);
    }


    /**
     * テンプレートキャッシュテスト
     *
     * ２回同じURIをreadしたらリソーステンプレートの時刻表時が違うはずです
     * @runInSeparateProcess
     */
    public function testResourceWithTemplateWithCache()
    {
        $params = array(
            'uri' => 'page://self/resource/template',
            'values' => array(),
            'options' => array('output' => 'html')
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#user_resource div#time');
        $original = $xml[0];
        sleep(1);
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#user_resource div#time');
        $cached = $xml[0];
        $this->assertNotSame($original, $cached);
    }

    /**
     * テンプレートキャッシュテスト
     *
     * テンプレートの時刻表時も同じはずです
     * @runInSeparateProcess
     */
    public function testResourceWithTemplateWithoutCache()
    {
        $params = array(
            'uri' => 'page://self/resource/template/cache',
            'values' => array(),
            'options' => array('output' => 'html')
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#user_resource div#time');
        $original = $xml[0];
        sleep(1);
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#user_resource div#time');
        $cached = $xml[0];
        $this->assertSame($original, $cached);
    }

    /**
     * リンクとテンプレート指定されたリソース
     * @runInSeparateProcess
     */
    public function testResourceWithTemplateAndLinkAndCache()
    {
        $params = array(
            'uri' => 'page://self/resource/link',
            'values' => array(),
            'options' => array('output' => 'html')
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content ul li');
        $expected = '<li>Athos</li>';
        $this->assertSame($expected, $xml[0]);
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#blog ul ul ul li.thumb');
        $expected = '<li class="thumb">コメントID(110)の評価(ID=121)</li>';
        $this->assertSame($expected, $xml[1]);
    }

    /**
     * リンクとテンプレート指定されたリソースにページャー付
     * @runInSeparateProcess
     */
    public function testResourceWithTemplateAndLinkAndPager()
    {
        $params = array(
            'uri' => 'page://self/resource/link/pager',
            'values' => array(),
            'options' => array('output' => 'html')
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content ul li.blog');
        $expected = '<li class="blog">Athos Blog</li>';
        $this->assertSame($expected, $xml[0]);
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#blog ul li.entry span.title');
        $expected = '<span class="title">PHP</span>';
        $this->assertSame($expected, $xml[0]);
    }

    /**
     * リンクとテンプレート指定されたリソースにページャー付
     * @runInSeparateProcess
     */
    public function testResourceWithTemplateAndLinkAndPagerWithPage2()
    {
        $params = array(
            'uri' => 'page://self/resource/link/pager',
            'values' => array(),
            'options' => array(
                'output' => 'html',
                'page' => 2
        )
        );
        $html = $this->_resource->read($params)->getBody();
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content ul li.blog');
        $expected = '<li class="blog">Athos Blog</li>';
        $this->assertSame($expected, $xml[0]);
        $xml = $this->_query->getXml($html, 'html#beardemo body div.content div#blog ul li.entry span.title');
        $expected = '<span class="title">JavaScript</span>';
        $this->assertSame($expected, $xml[0]);
    }
}
