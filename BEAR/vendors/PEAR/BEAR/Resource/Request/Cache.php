<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Cache.php 935 2009-09-21 15:54:38Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * リソースキャッシュクラス
 *
 * <pre>
 * リソースリクエストをキャッシュするクラスです。キャッシュするオブジェクトかそうでないかをfacotryで判断しています。
 * DIコンテナでBEAR_Resource_Requestクラスに注入されます。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Cache.php 935 2009-09-21 15:54:38Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
class BEAR_Resource_Request_Cache extends BEAR_Factory
{

    /**
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * factory
     *
     * <pre>
     * DIコンテナで生成されBEAR_Resource_Requesオブジェクトに注入されます
     * </pre>
     *
     * @param array $config コンフィグ
     *
     * @return object
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
     * リクエスト実行クラスを返す
     *
     * <pre>
     * 実行オブジェクトを作成して返します
     * </pre>
     *
     * @param string $uri    URI
     * @param array  $values 引数
     * @param string $method リクエストメソッド
     *
     * @return BEAR_Ro
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
        $cache->setLife($options['cache']['life']);
        $saved = $cache->get($cacheKey);
        if ($saved) {
            // キャッシュ読み込み
            $ro = BEAR::factory($saved['class'], $saved['config']);
            $ro->setCode($saved['code']);
            $headers = is_array($saved['headers']) ? $saved['headers'] : array();
            $ro->setHeaders($headers);
            $ro->setBody($saved['body']);
            unset($saved);
        } else {
            // キャッシュ書き込み
            $obj = BEAR::factory('BEAR_Resource_Execute', $this->_config);
            $ro = $obj->request($this->_config['method'], $this->_config['uri'], $this->_config['values'], $this->_config['options']);
            if (!PEAR::isError($ro)) {
                $save = array('class'=>get_class($ro), 'config'=>$this->_config, 'code'=>$ro->getCode(), 'headers'=>$ro->getHeaders(), 'body'=>$ro->getBody());
                $cache->set($cacheKey, $save);
            } else {
                // キャッシュ生成エラー
                $msg = 'Resource Cache Write Failed';
                $info = array(
                    'ro class' => get_class($ro));
                throw $this->_exception($msg, array(
                    'info' => $info));
            }
        }
        return $ro;
    }
}
