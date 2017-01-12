<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category     BEAR
 * @package      BEAR_Test
 * @author       Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright    2008-2017 Akihito Koriyama  All rights reserved.
 * @license      http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link         https://github.com/bearsaturday
 */

/**
 * BEAR
 *
 * @category  BEAR
 * @package   BEAR_Test
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2017 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 *
 * @Singleton
 */
class BEAR_Test_Client extends HTTP_Request2
{
    /**
     * Http client
     *
     * @var \HTTP_Request2
     * @see http://pear.php.net/manual/en/package.http.http-client.http-client.http-client.php
     */
    public $request;

    /**
     * Http response
     *
     * @var HTTP_Request2_Response
     * @see http://pear.php.net/manual/en/package.http.http-request2.intro.php
     */
    public $response;

    /**
     * @param HTTP_Request2 $request
     */
    public function __construct(HTTP_Request2 $request = null)
    {
        $this->request = $request ? $request : new HTTP_Request2;
    }

    /**
     * Http request
     *
     * @param string $method
     * @param string $url
     * @param array  $submit
     * @param string $formName
     *
     * @return HTTP_Request2_Response
     */
    public function request($method, $url, array $submit = array(), $formName = 'form')
    {
        $this->request->setMethod($method);
        $this->request->setUrl(new Net_URL2($url));

        if ($submit) {
            $submit = array_merge(array('_token' => '0dc59902014b6', '_qf__' . $formName => ''), $submit);
        }
        if ($submit && $this->request->getMethod() === HTTP_Request2::METHOD_POST) {
            $this->request->addPostParameter($submit);
        }
        if ($submit && $this->request->getMethod() === HTTP_Request2::METHOD_GET) {
            $url->setQueryVariables($submit);
        }
        $this->response = $this->request->send();
        return $this;
    }

    /**
     * Get form log
     *
     * @return array
     */
    public function getFormLog()
    {
        $header = $this->response->getHeader();
        $formInfo = json_decode($header['x-bear-form-log'], true);
        $result = isset($formInfo[0]) ? $formInfo[0] : null;
        return $result;
    }

    /**
     * Get is valid submit
     *
     * @return bool
     */
    public function isValidSubmit()
    {
        $formLog = $this->getFormLog();
        $result = (isset($formLog['valid']) && $formLog['valid'] === true) ? true : false;
        return $result;
    }


    /**
     * Get form errors
     *
     * @return array
     */
    public function getFormErrors()
    {
        $formLog = $this->getFormLog();
        $result = isset($formLog['errors']) ? $formLog['errors'] : array();
        return $result;
    }

    /**
     * Get resource request log
     *
     * @return array<string>
     */
    public function getResourceRequestLog()
    {
        $header = $this->response->getHeader();
        $result = isset($header['x-bear-resource-log']) ? json_decode($header['x-bear-resource-log'], true) : array();
        return $result;
    }
}
