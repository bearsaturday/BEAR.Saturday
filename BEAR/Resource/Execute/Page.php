<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Pageリソース
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
     * @throws BEAR_Exception
     */
    public function request()
    {
        $this->_setGetPost($this->_config['options']);
        //$pageRawPath = substr($this->_config['uri'], 7);
        $url = parse_url($this->_config['uri']);
        $pageRawPath = $url['path'];
        $pageClass = 'page' . str_replace('/', '_', $pageRawPath);
        if (! class_exists($pageClass, false)) {
            $pageFile = str_replace('/', DIRECTORY_SEPARATOR, $pageRawPath) . '.php';
            BEAR_Main::includePage($pageFile);
        }
        if (! class_exists($pageClass, false)) {
            throw new BEAR_Exception("Page class[${pageClass}] is not exist.");
        }
        $pageConfig = ['resource_id' => $pageClass, 'mode' => BEAR_Page::CONFIG_MODE_RESOURCE];
        $pageOptions = $this->_config['options'];
        if (isset($this->_config['options']['page'])) {
            $pageConfig = array_merge($pageConfig, (array) $this->_config['options']['page']);
        }
        if (isset($pageConfig['ua'])) {
            $pageConfig['enable_ua_sniffing'] = true;
        }
        $page = BEAR::factory($pageClass, $pageConfig, $pageOptions);
        /** @var $page BEAR_Page */
        $method = ($this->_config['method'] === 'read') ? 'onInit' : 'onAction';
        $args = array_merge($page->getArgs(), $this->_config['values']);
        $cnt = $this->_roPrototye->countStack();
        $page->{$method}($args);
        $cnt = $this->_roPrototye->countStack() - $cnt;
        // リソースモード
        switch (true) {
            // resource
            case ! isset($this->_config['options']['output']) || $this->_config['options']['output'] === 'resource':
                $result = $this->_outputResource($page, $cnt);

                break;
            // html
            case $this->_config['options']['output'] === 'html':
                $result = $this->_outputHtml($page);

                break;
            default:
                $info = ['output option' => $this->_config['options']['output']];

                throw $this->_exception('Unknown page resource options', compact('info'));

                break;
        }
        if (! ($result instanceof BEAR_Ro)) {
            $result = BEAR::factory('BEAR_Ro', [])->setBody($result);
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
     * @throws BEAR_Resource_Execute_Exception
     *
     * @return array
     */
    protected function _outputResource(BEAR_Page &$page, $cnt)
    {
        // BEAR_Page::set()でsetされた値
        $pageValues = $page->getValues();
        $result = [];
        for ($i = 0; $i < $cnt; $i++) {
            $item = $this->_roPrototye->pop();
            $key = key($item);
            $prototypeRo = current($item);
            /* @var $prototypeRo BEAR_Ro_Prototype */
            $result[$key] = $prototypeRo->getValue();
        }
        $result = array_merge($result, (array) $pageValues);
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
