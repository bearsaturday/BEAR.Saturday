<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Page.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link       https://github.com/bearsaturday
 */

/**
 * Pageリソース
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: Page.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Execute_Page extends BEAR_Resource_Execute_Adapter
{
    /**
     * Roプロトタイプ
     *
     * @var BEAR_Ro_Prototype
     */
    protected $_roPrototye;

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $this->_roPrototye = BEAR::dependency('BEAR_Ro_Prototype');
    }

    /**
     * リソースリクエスト実行
     *
     * <pre>
     * htdocs/のページをリソースとして扱うクラスです。
     * readがページクラスのonInit()を呼び出しonInit内でsetされたものが結果になって帰ります。フォーマットはROです。
     * create, update, deleteはonAction()を呼び出します。
     *
     * $this->_config['options']に応じて次のどちらをpageリソースにするか決定されます。
     *
     *   1)pageが出力するHTML
     *   2)set()でセットされたリソース結果の集合
     * </pre>
     *
     * @return mixed
     * @throws BEAR_Exception
     */
    public function request()
    {
        $this->_setGetPost($this->_config['options']);
        //$pageRawPath = substr($this->_config['uri'], 7);
        $url = parse_url($this->_config['uri']);
        $pageRawPath = $url['path'];
        $pageClass = 'page' . str_replace('/', '_', $pageRawPath);
        if (!class_exists($pageClass, false)) {
            $pageFile = str_replace('/', DIRECTORY_SEPARATOR, $pageRawPath) . '.php';
            BEAR_Main::includePage($pageFile);
        }
        if (!class_exists($pageClass, false)) {
            throw new BEAR_Exception("Page class[$pageClass] is not exist.");
        }
        $pageConfig = array('resource_id' => $pageClass, 'mode' => BEAR_Page::CONFIG_MODE_RESOURCE);
        $pageOptions = $this->_config['options'];
        if (isset($this->_config['options']['page'])) {
            $pageConfig = array_merge($pageConfig, (array)$this->_config['options']['page']);
        }
        if (isset($pageConfig['ua'])) {
            $pageConfig['enable_ua_sniffing'] = true;
        }
        $page = BEAR::factory($pageClass, $pageConfig, $pageOptions);
        /** @var $page BEAR_Page  */
        $method = ($this->_config['method'] === 'read') ? 'onInit' : 'onAction';
        $args = array_merge($page->getArgs(), $this->_config['values']);
        $cnt = $this->_roPrototye->countStack();
        $page->$method($args);
        $cnt = $this->_roPrototye->countStack() - $cnt;
        // リソースモード
        switch (true) {
            // resource
            case (!isset($this->_config['options']['output']) || $this->_config['options']['output'] === 'resource'):
                $result = $this->_outputResource($page, $cnt);
                break;
            // html
            case ($this->_config['options']['output'] === 'html'):
                $result = $this->_outputHtml($page);
                break;
            default:
                $info = array('output option' => $this->_config['options']['output']);
                throw $this->_exception('Unknown page resource options', compact('info'));
                break;
        }
        if (!($result instanceof BEAR_Ro)) {
            $result = BEAR::factory('BEAR_Ro', array())->setBody($result);
        }
        return $result;
    }

    /**
     * オプションをセット
     *
     * ページャー番号、$_GET, $_POSTを指定できます。
     *
     * <ul>
     * <li>'pager' int   ページ番号</li>
     * <li>'get'   array $_GET</li>
     * <li>'post'  array $_POST</li>
     * </ul>
     *
     * @param array $options
     *
     * @return void
     */
    protected function _setGetPost(array $options)
    {
        if (isset($options['page'])) {
            $_REQUEST['_start'] = $options['page'];
        }
        if (isset($options['get']) && is_array($options['get'])) {
            $_GET = array_merge($_GET, $options['get']);
        }
        if (isset($options['post']) && is_array($options['post'])) {
            $_POST = array_merge($_POST, $options['post']);
        }
    }

    /**
     * Pageリソースをリソースとして出力
     *
     * @param BEAR_Page &$page ページ
     * @param int       $cnt   プロトタイプリソースのスタックカウンタ
     *
     * @return array
     * @throws BEAR_Resource_Execute_Exception
     */
    protected function _outputResource(BEAR_Page &$page, $cnt)
    {
        // BEAR_Page::set()でsetされた値
        $pageValues = $page->getValues();
        $result = array();
        for ($i = 0; $i < $cnt; $i++) {
            $item = $this->_roPrototye->pop();
            list($key, $prototypeRo) = each($item);
            /* @var $prototypeRo BEAR_Ro_Prototype */
            $result[$key] = $prototypeRo->getValue();
        }
        $result = array_merge($result, (array)$pageValues);
        // $page->setPrototypeRo();
        return $result;
    }

    /**
     * PageリソースをHTMLとして出力
     *
     * @param BEAR_Page &$page ページ
     *
     * @return BEAR_Ro
     */
    protected function _outputHtml(BEAR_Page &$page)
    {
        $page->setPrototypeRo();
        $page->onOutput();
        $ro = $page->getPageRo();
        return $ro;
    }
}
