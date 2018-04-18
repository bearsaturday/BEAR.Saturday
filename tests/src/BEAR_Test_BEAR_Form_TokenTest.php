<?php

$bearDemoPath = dirname(dirname(__DIR__)) . '/vendor/bearsaturday/beardemo.local';
require_once $bearDemoPath . '/App.php';

/**
 * BEAR
 *
 * @category   BEAR
 * @package    Test
 * @subpackage BEAR_Form
 */

/**
 * @category   BEAR
 * @package    Test
 * @subpackage resource
 */
class BEAR_Test_BEAR_Form_TokenTest extends PHPUnit_Framework_TestCase
{
    var $_token;

    public function setUp()
    {
        restore_error_handler();
        if (!BEAR::exists('BEAR_Session')) {
            BEAR::set('BEAR_Session', BEAR::factory('BEAR_Session', array('adapter' => 0, 'prefix' => '')));
        }
        $this->_token = new BEAR_Form_Token(array());
        $this->_token->onInject();
    }

    public function testNewSessionToken()
    {
        $this->_token->newSessionToken();
        $token = BEAR::dependency('BEAR_Session')->get(BEAR_Form_Token::SESSION_TOKEN);
        $this->assertTrue(is_string($token));
    }

    public function testgetToken()
    {
        $this->_token->newSessionToken();
        $_POST['_tone'] = $_SESSION['beardemo0.0.20stoken'];
        $token = $this->_token->getToken();
        $this->assertTrue(is_string($token));
    }

    public function test_isTokenCsrfValidSuccess()
    {
        $val = '03c88eb462460abeeff7' . '6ca881e559ef98901975';
        $_POST['_token'] = $val;
        $token = new BEAR_Form_Token(array());
        $token->onInject();
        BEAR::dependency('BEAR_Session')->set(BEAR_Form_Token::SESSION_TOKEN, $val);
        $isValid = $token->isTokenCsrfValid();
        $this->assertTrue($isValid);
    }

    public function test_isTokenCsrfValidFault()
    {
        $val1 = '03c88eb462460abeeff7' . '6ca881e559ef98901975';
        $val2 = '00000000000000000000' . '6ca881e559ef98901975';
        $_POST['_token'] = $val1;
        $token = new BEAR_Form_Token(array());
        $token->onInject();
        BEAR::dependency('BEAR_Session')->set(BEAR_Form_Token::SESSION_TOKEN, $val2);
        $isValid = $token->isTokenCsrfValid();
        $this->assertFalse($isValid);
    }

    public function test_isTokenPoeValidSuccess()
    {
        $val = '03c88eb462460abeeff7' . '6ca881e559ef98901975';
        $val2 = '00000000000000000000' . '6ca881e559ef98901975';
        $_POST['_token'] = $val;
        $token = new BEAR_Form_Token(array());
        $token->onInject();
        BEAR::dependency('BEAR_Session')->set(BEAR_Form_Token::SESSION_POE, array($val2));
        $isValid = $token->isTokenPoeValid();
        $this->assertTrue($isValid);
    }

    public function test_isTokenPoeValidFailure()
    {
        $val = '03c88eb462460abeeff7' . '6ca881e559ef98901975';
        $_POST['_token'] = $val;
        $token = new BEAR_Form_Token(array('debug' => 0));
        $token->onInject();
        BEAR::dependency('BEAR_Session', array('debug' => 0))->set(BEAR_Form_Token::SESSION_POE, array($val));
        $isValid = $token->isTokenPoeValid();
        $this->assertFalse($isValid);
    }
}