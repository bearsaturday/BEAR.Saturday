<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Prototype
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net/
 */

/**
 * リソースプロトタイプ
 *
 * リソースのリクエストです。
 * リソースプロトタイプがリソースをどのように実行するかを保持し、実行されリソースリクエスト結果が得られます。
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Prototype
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$
 * @link       http://www.bear-project.net/
 */
class BEAR_Ro_Prototype extends BEAR_Ro
{
    /**
     * リソースプロトタイプスタック
     *
     * @var string
     */
    protected static $_stack = array();

    /**
     * リソースリンク
     *
     * <pre>
     * フォーマット
     *
     * $_chainLink[] = array('link1');
     * $_chainLink[] = array('link2', 'link3');
     * </pre>
     *
     * @var array
     */
    protected $_chainLink = array();

    /**
     * BEAR_Ro_Prototype_Link
     *
     * @var mixed
     */
    protected $_prototypeLink = array();

    /**
     * setオプション
     *
     * @var array
     */
    protected $_setOption = 'body';

    /**
     * 取得したRo
     *
     * @var BEAR_Ro
     */
    protected $_ro;

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
    }

    /**
     * スタックされたRoプロトタイプを１つ取り出す
     *
     * @return array
     */
    public function pop()
    {
        return array_pop(self::$_stack);
    }

    /**
     * スタックされたRoプロトタイプを全て取り出す
     *
     * @return array
     */
    public function popAll()
    {
        $result = self::$_stack;
        self::$_stack = array();
        return $result;
    }

    /**
     * Roプロトタイプのスタックの数の取得
     *
     * @return int
     */
    public function countStack()
    {
        return count(self::$_stack);
    }

    /**
     * リソースリンクを取得
     *
     * リソースのリンクを取得します。
     * リンクはリンクキーをキーにリンクURIを値にした配列をROリソースの中のonLinkメソッドで返す事で実現できます。
     *
     * @param mixed $link
     *
     * @return BEAR_Ro
     */
    public function link($link)
    {
        $this->_chainLink[] = is_array($link) ? $link : array($link);
        return $this;
    }

    /**
     * リソースリクエスト実行
     *
     * @return void
     */
    protected function _doRequest()
    {
        $this->_ro = BEAR::factory('BEAR_Resource_Request', $this->_config['request'])->request();
    }

    /**
     * リソースリクエスト実行
     *
     * @return BEAR_Ro
     */
    public function request()
    {
        $this->_doRequest();
        $this->_setHtml($this->hasChainLink());
        return $this->_ro;
    }

    /**
     * 値を取得
     *
     * リソースリクエストを行いテンプレートオプションが適用した文字列が、
     * そうでなければリソースボディを返します。
     *
     * @return mixed
     */
    public function getValue()
    {
        $ro = $this->request();
        if (isset($this->_config['request']['options']['template'])) {
            $result = $ro->getHtml();
            if ($this->_config['debug'] === true) {
                $result = BEAR::dependency('BEAR_Ro_Debug')->getResourceToString($ro);
            }
        } else {
            $result = $ro->getBody();
        }
        return $result;
    }

    /**
     * リソーステンプレートをRoにセット
     *
     * リクエストにテンプレートオプションが指定されているとHTML等文字列化してRoに保持します。
     *
     * @todo リソースボディのキャッシュはUA共通に
     *
     * @param $isLinked
     */
    protected function _setHtml($isLinked)
    {
        // キャッシュ?
        $isLinkCache = isset($this->_config['request']['options']['cache']['link']) && $this->_config['request']['options']['cache']['link'];
        if ($isLinked && $isLinkCache === true) {
            $life = $this->_config['request']['options']['cache']['life'];
        } elseif (!$isLinked && isset($this->_config['request']['options']['cache']['life']) && isset($this->_config['request']['options']['template'])) {
            $life = $this->_config['request']['options']['cache']['life'];
        } else {
            $life = false;
        }
        if ($life !== false) {
            // キャッシュ読み込み
            $cache = BEAR::dependency('BEAR_Cache')->setLife($life);
            $pagerKey = isset($_GET['_start']) ? $_GET['_start'] : '';
            $ua = BEAR::get('page')->getConfig('ua');
            $cacheKey = $ua . md5(serialize($this->_config['request']) . "-{$pagerKey}");
            $saved = $cache->get($cacheKey);
            if ($saved) {
                $this->_ro = $saved;
                return;
            } else {
                $useCache = true;
            }
        }
        //実リクエスト
        $body = ($isLinked !== true) ? $this->_ro->getBody() : $this->getLinkedBody();
        $this->_ro->setBody($body);
        if ($isLinked === true) {
            $this->_ro->setHeader('_linked', $this->_chainLink);
        }
        // テンプレート適用
        $html = (isset($this->_config['request']['options']['template'])) ? $this->_getHtml($body) : false;
        if ($html !== false) {
            $this->_ro->setHtml($html);
        }
        // キャッシュ書き込み
        if (isset($useCache)) {
            $roContainer = new BEAR_Ro_Container($this->_ro);
            $cache->set($cacheKey, $roContainer);
        }
    }

    /**
     * リソーステンプレートに適用さたHTML文字列を取得
     *
     * @param mixed $body リソースボディ
     *
     * @return string
     */
    protected function _getHtml($body)
    {
        if (BEAR::exists('App_Main')) {
            $enableUaSniffing = BEAR::dependency('App_Main')->getConfig('enable_ua_sniffing');
        } else {
            $enableUaSniffing = false;
        }
        if ($enableUaSniffing === true) {
            $fullPath = BEAR::dependency('BEAR_Agent')->getRoleFile(
                _BEAR_APP_HOME . $this->_config['path'] . 'elements',
                $this->_config['request']['options']['template'],
                'tpl'
            );
        } else {
            $fullPath = _BEAR_APP_HOME . $this->_config['path'] . 'elements/' . $this->_config['request']['options']['template'] . '.tpl';
        }
        if (realpath($fullPath)) {
            $smarty = BEAR::dependency('BEAR_Smarty');
            $smarty->assign('body', $body);
            $result = $smarty->fetch($fullPath);
        } else {
            $errorMsg = "Invalid template path [{$fullPath}]";
            trigger_error($errorMsg, E_USER_WARNING); // __toStringでは例外NG
            if ($this->_config['debug']) {
                $result = '<span style="border: 1px solid red;">' . $errorMsg . '</span>';
            }
        }
        return $result;
    }

    /**
     * リソースリクエストをshutdown時に実行
     *
     * @return BEAR_Ro
     */
    public function requestOnShutdown()
    {
        BEAR::dependency('BEAR_Ro_Shutdown')->register()->set($this);
        return $this;
    }

    /**
     * リソースセット
     *
     * プロトタイプリソースをpageにsetします。$setOptionsでセットのオプションを指定します。
     *
     * @param string $key       リソースキー
     * @param string $setOption セットオプション
     *
     * @return BEAR_Ro_Prototype
     */
    public function set($key = null, $setOption = 'value')
    {
        //キー省略
        if (!$key) {
            // 未指定の場合://と/を_に変換してアサイン名に
            $config = $this->getConfig();
            $key = strtolower(str_replace('/', '_', $config['request']['uri']));
        }
        $this->_setOption = $setOption;
        // push prototype
        self::$_stack[] = array($key => $this);
        return $this;
    }

    /**
     * リソースボディを取得
     *
     * リソースリクエストを行いその結果のボディを返します。
     *
     * @param bool $link
     *
     * @return array|mixed
     */
    public function getBody($link = false)
    {
        if ($link === true) {
            $result = $this->getLinkedBody();
        } else {
            $this->_doRequest();
            $result = $this->_ro->getBody();
        }
        return $result;

    }

    /**
     * リソースヘッダーを取得（アイテム)
     *
     * リソースリクエストを行いその結果のヘッダーを返します。
     *
     * @param $headerKey
     *
     * @return null
     */
    public function getHeader($headerKey)
    {
        $this->_doRequest();
        $result = $this->_ro->getHeader($headerKey);
        return $result;
    }

    /**
     * リソースヘッダーを取得（リスト)
     *
     * リソースリクエストを行いその結果のヘッダーを返します。
     *
     * @return array
     */
    public function getHeaders()
    {
        $this->_doRequest();
        $result = $this->_ro->getHeaders();
        return $result;
    }

    /**
     * リンクを持つか
     *
     * @return bool
     */
    public function hasChainLink()
    {
        return ($this->_chainLink !== array());
    }

    /**
     * リンクされたbody配列を取得
     *
     * @return array
     */
    public function getLinkedBody()
    {
        $this->_doRequest();
        $result = BEAR::dependency('BEAR_Ro_Prototype_Link', $this->_prototypeLink)->chainLink(
            $this->_ro,
            $this->_chainLink
        );
        return $result;
    }

    /**
     * Ro取得
     *
     * @return BEAR_Ro
     */
    public function getRo()
    {
        $this->_doRequest();
        $ro = $this->_ro->getRo();
        return $ro;
    }

    /**
     * setオプションの取得
     *
     * @var array
     *
     * @return string
     */
    public function getSetOption()
    {
        return $this->_setOption;
    }

    /**
     * デバック表示
     *
     * <code>
     * $resource->$read($params)->p();
     * $resource->$read($params)->set('user')->p();
     * </code>
     *
     * @return BEAR_Ro
     */
    public function p()
    {
        if ($this->_config['debug'] === true) {
            $this->_p();
        }
        return $this;
    }

    /**
     * デバック表示描画
     *
     * @return BEAR_Ro
     * @ignore
     */
    protected function _p()
    {
        $trace = debug_backtrace();
        $trace = $trace[1];
        $place = " in <span style=\"color: gray;\">{$trace['file']}</span> on line {$trace['line']} ";
        if ($this->hasChainLink()) {
            $linkBody = $this->getLinkedBody();
        } else {
            //$body = $this->getBody();
        }
        $headers = $this->_ro->getHeaders();
        $request = $headers['_request'];
        foreach ($headers as $key => $header) {
            if (substr($key, 0, 1) == '_') {
                unset($headers[$key]);
            }
        }
        $linkLabel = '';
        if ($this->_chainLink) {
            foreach ($this->_chainLink as $links) {
                $linkLabel .= '->' . (count($links) > 1 ? '(' . implode(',', $links) . ')' : $links[0]);
            }
        }
        $label = $this->_config['uri'] . $linkLabel;
        $labelField = '<fieldset style="color:#4F5155; border:1px solid black;padding:2px;width:10px;">';
        $labelField .= '<legend style="color:black;font-size:9pt;font-weight:bold;font-family:sans-serif;">' . $label . '</legend>';
        echo $labelField;
        if (isset($linkBody)) {
            print_a($linkBody);
        } else {
            $resource = array(
                'code' => $this->getCode(),
                'header' => $headers,
                'body' => $this->getBody(),
                'link' => $this->getLinks()
            );
            print_a($resource);
        }
        echo '</fieldset>';
        $linkLabel = $linkLabel ? ' and link(s)' : '';
        echo "by \"{$request}\"$linkLabel {$place}<br /><br />";
        return $this;
    }
}
