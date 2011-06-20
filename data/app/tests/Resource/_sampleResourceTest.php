<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id:$
 * @link      http://www.bear-project.net/
 */

/**
 * BEAR
 *
 * @category  BEAR
 * @package
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   Release: @package_version@
 * @link      http://www.bear-project.net/
 */
class Test_Ro_Test_User_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * for MDB2
     *
     * @var bool
     */
    protected $backupGlobals = FALSE;

    /**
     * @var BEAR_Resource
     */
    public $resource;

    public function setUp()
    {
        $config = array('path' => __DIR__ . 'App/Ro');
        $this->resource =  new BEAR_Resource($config);
        $this->uri = 'Test/User';
    }

    /**
     * read User?id=1
     */
    public function testReadId1()
    {
        $params = array(
            'uri' => $this->uri,
            'values' => array('id' => 1),
            'options' => array()
        );
        $ro = $this->resource->read($params)->getRo();
        $this->assertSame(200, $ro->getCode());
        $body = $ro->getBody();
        $expected = "サンデー";
        $this->assertSame($expected, $body['name']);
    }
}