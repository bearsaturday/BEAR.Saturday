<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * リソースオブジェクトリンク
 *
 * リソースリンクを処理します。BEAR_Ro_Prototype::getLinkedBody()から利用されています。
 */
class BEAR_Ro_Prototype_Link extends BEAR_Base
{
    /**
     * @var array
     */
    protected $_chain = [];

    protected $_links;

    /**
     * リソースのリンクをつなげる
     *
     * リソースをリンクした結果をHEADER_LINK_BODYヘッダーに付加します
     *
     * @return array
     */
    public function chainLink(BEAR_Ro $rootRo, array $chain)
    {
        if ($chain === []) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }
        $config = $rootRo->getConfig();
        if (! isset($config['options']['cache']['link'])) {
            $result = $this->_chainLink($rootRo, $chain);
        } else {
            $cacheKey = serialize($chain);
            $cache = BEAR::dependency('BEAR_Cache');
            if (isset($config['options']['cache']['life'])) {
                $cache->setLife($config['options']['cache']['life']);
            }
            $result = $cache->get($cacheKey);
            if (! $result) {
                $result = $this->_chainLink($rootRo, $chain);
            }
        }

        return $result;
    }

    /**
     * リソースのリンクをつなげる
     *
     * リソースをリンクした結果をHEADER_LINK_BODYヘッダーに付加します
     *
     * @return array
     */
    private function _chainLink(BEAR_Ro $rootRo, array $chain)
    {
        $config = $rootRo->getConfig();
        $this->_chain = $chain;
        $hasMultiLink = $hasMultiLink2 = false;
        $class = get_class($rootRo);
        assert(method_exists($class, 'onLink'));
        // ルートリソース
        $firstUri = strtolower(str_replace('/', '_', $config['uri']));
        $link = $body = $rootRo->getBody();
        $isCollection = (count($body) !== count($body, COUNT_RECURSIVE));
        if ($isCollection === true) {
            $sourceBody = $body;
            self::_makeCollectionChain($sourceBody, $firstUri, $rootRo);
            $linkFrom = $firstUri;
            $linked = [$firstUri => $sourceBody];
            $linked[$firstUri] = $sourceBody;
            $body = $sourceBody;
            $isCollectionRoot = true;
        } else {
            $body = $sourceBody = $body;
            $linked = [$firstUri => $sourceBody];
            $isCollectionRoot = false;
        }
        $ro = $rootRo;

        foreach ($this->_chain as $links) {
            foreach ($links as $link) {
                if ($hasMultiLink2 === true || $isCollectionRoot) {
                    self::_changeRecursive($linked, $linkFrom, $link);
                } else {
                    $onLinks = $ro->onLink($sourceBody);
                    $config = self::_makeRequestConfig($onLinks, $link);
                    $linkRo = BEAR::factory('BEAR_Resource_Request', $config)->request();
                    $links = $linkRo->getLinks();
                    if (isset($links['pager'])) {
                        $this->_links['pager'] = $links['pager'];
                    }
                    $body = $linkRo->getBody();
                    $isCollection = (count($body) != count($body, COUNT_RECURSIVE));
                    if ($isCollection) {
                        self::_makeCollectionChain($body, $link, $linkRo);
                        $hasMultiLink = true;
                        $linkFrom = $link;
                    }
                    $linked[$link] = $body;
                }
            }
            $linkFrom = $link;
            $sourceBody = $body;
            $hasMultiLink2 = $hasMultiLink ? true : false;
            $ro = $linkRo;
        }
        $this->_cleanUpLink($linked);

        return $linked;
    }

    /**
     * リンクリソースを再帰で変更
     *
     * @param array  &$linked
     * @param string $linkFrom
     * @param string $linkTo
     */
    private static function _changeRecursive(&$linked, $linkFrom, $linkTo)
    {
        $isLink = array_key_exists('_link', $linked);
        $isSet = isset($linked['_link'][$linkFrom]);
        if ($isLink && $isSet) {
            $values = $linked['_link'][$linkFrom]['values'];
            $class = $linked['_link'][$linkFrom]['class'];
            $onLinks = @$class::onLink($values);
            //            $params = $onLinks[$linkTo];
            $config = self::_makeRequestConfig($onLinks, $linkTo);
            $ro = BEAR::factory('BEAR_Resource_Request', $config)->request();
            //            $ro = BEAR::dependency('BEAR_Resource')->read($params)->getRo();
            $body = $ro->getBody();
            if (is_array($body)) {
                self::_makeCollectionChain($body, $linkTo, $ro);
            }
            $linked[$linkTo] = $body;
        } else {
            foreach ($linked as &$val) {
                if (is_array($val)) {
                    self::_changeRecursive($val, $linkFrom, $linkTo);
                }
            }
        }
    }

    /**
     * リンクのクリーンアップ
     *
     * <pre>リンクマークを除去</pre>
     *
     * @param array &$data データ
     */
    private function _cleanUpLink(&$data)
    {
        $this->_chain = [];
        if (! is_array($data)) {
            return;
        }
        foreach ($data as &$val) {
            if (is_array($val) && isset($val['_link'])) {
                unset($val['_link']);
            }
            $this->_cleanUpLink($val);
        }
    }

    /**
     * リンクリソースからリンクマークを設定
     *
     * <pre>
     * リンクリソースリクエストを行う場所を特定するために配列データに'_link'というリンクマークをつけます。
     * つけられたマークは後でまとめて変換が行われます
     * </pre>
     *
     * @param array   &$body リンクリソース
     * @param string  $link  リンク
     * @param BEAR_Ro $ro    リソースオブジェクト
     */
    private static function _makeCollectionChain(array &$body, $link, $ro)
    {
        foreach ($body as &$row) {
            if (is_array($row)) {
                $row['_link'][$link] = ['values' => $row, 'class' => get_class($ro)];
            } elseif (! isset($body['_link'][$link])) {
                $body['_link'][$link] = ['values' => $body, 'class' => get_class($ro)];
            }
        }
    }

    /**
     * Request Condifg作成
     *
     * $onLinks, $linkからRequest Configを作成します。
     *
     * @param array  $onLinks
     * @param string $link
     *
     * @throws BEAR_Ro_Prototype_Link_Exception
     *
     * @return array
     * @ignore
     */
    private static function _makeRequestConfig($onLinks, $link)
    {
        if (! isset($onLinks[$link])) {
            $info = [
                'link uri' => $link,
                'available links' => $onLinks
            ];

            throw new BEAR_Ro_Prototype_Link_Exception('Resource link key is not exist.', [
                'code' => BEAR::CODE_BAD_REQUEST,
                'info' => $info
            ]);
        }
        if (is_array($onLinks[$link])) {
            $emptyParams = [
                'method' => 'read',
                'uri' => '',
                'values' => [],
                'options' => []
            ];
            $result = array_merge($emptyParams, $onLinks[$link]);
        } else {
            $result = [
                'method' => 'read',
                'uri' => $onLinks[$link],
                'values' => [],
                'options' => []
            ];
        }

        return $result;
    }
}
