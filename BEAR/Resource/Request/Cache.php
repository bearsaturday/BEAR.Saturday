<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Cache.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link      https://github.com/bearsaturday
 */

/**
 * リソースキャッシュ
 *
 * リソースリクエストをキャッシュするクラスです。
 * リソースリクエストキャッシュオプション(['options']['cache'])が指定されているとキャッシュオブジェクトを返し、
 * そうでないとリクエストオブジェクトを返します。
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Cache.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link      https://github.com/bearsaturday
 */
class BEAR_Resource_Request_Cache extends BEAR_Factory
{
    /**
     * factory
     *
     * DIコンテナで生成されBEAR_Resource_Requesオブジェクトに注入されます
     *
     * @return stdClass
     */
    public function factory()
    {
        $isCacheRequst = isset($this->_config['options']['cache']) && !(isset($_GET['_cc']));
        if ($isCacheRequst === true) {
            $obj = $this;
        } else {
            $obj = BEAR::factory('BEAR_Resource_Execute', $this->_config);
        }
        return $obj;
    }

    /**
     * リソースリクエスト
     *
     * スキーマや拡張子に応じたリソースリクエストオブジェクトを返します
     *
     * @return BEAR_Ro
     * @throws BEAR_Resource_Request_Exception
     */
    public function request()
    {
        $options = $this->_config['options'];
        if (isset($options['cache']['key'])) {
            $cacheKey = $options['cache']['key'];
        } else {
            $pagerKey = isset($_GET['_start']) ? $_GET['_start'] : '';
            $sconf = serialize($this->_config);
            $cacheKey = "{$this->_config['uri']}{$sconf}-{$pagerKey}";
            //set
            $options['cache']['key'] = $cacheKey;
        }
        $cacheKey = $this->_config['uri'] . md5($cacheKey);
        // キャッシュ
        $cache = BEAR::dependency('BEAR_Cache');
        if (isset($options['cache']['life'])) {
            $cache->setLife($options['cache']['life']);
        }
        $saved = $cache->get($cacheKey);
        if ($saved) {
            // キャッシュ読み込み
            $ro = BEAR::factory($saved['class'], $saved['config']);
            $ro->setCode($saved['code']);
            $headers = is_array($saved['headers']) ? $saved['headers'] : array();
            $ro->setHeaders($headers);
            $ro->setBody($saved['body']);
            $ro->setLinks($saved['links']);
            if (isset($saved['links']['pager'])) {
                BEAR::dependency('BEAR_Pager')->setPagerLinks(
                    $saved['links']['pager']['links'],
                    $saved['links']['pager']['info']
                );
            }
            unset($saved);
        } else {
            // キャッシュ書き込み
            $obj = BEAR::factory('BEAR_Resource_Execute', $this->_config);
            $ro = $obj->request(
                $this->_config['method'],
                $this->_config['uri'],
                $this->_config['values'],
                $this->_config['options']
            );
            if (!PEAR::isError($ro)) {
                $save = array(
                    'class' => get_class($ro),
                    'config' => $this->_config,
                    'code' => $ro->getCode(),
                    'headers' => $ro->getHeaders(),
                    'body' => $ro->getBody(),
                    'links' => $ro->getLinks()
                );
                if (isset($options['cache']['life'])) {
                    $cache->setLife($options['cache']['life']);
                }
                $cache->set($cacheKey, $save);
            } else {
                // キャッシュ生成エラー
                $msg = 'Resource Cache Write Failed';
                $info = array('ro class' => get_class($ro));
                throw $this->_exception($msg, array('info' => $info));
            }
        }
        return $ro;
    }
}
