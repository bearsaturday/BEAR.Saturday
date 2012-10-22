<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: None.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 */
/**
 * キャッシュしないキャッシュクラス
 *
 * <pre>
 * キャッシュを使用しない場合のどんなアクセスをしてもfalseしか返さないクラスです。
 * キャッシュ使用時とのパフォーマンスチェック等にも使えます。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Cache
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: None.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Cache/BEAR_Cache.html
 *
 */
final class BEAR_Cache_Adapter_None extends BEAR_Base
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
     * キャッシュ無効メソッド
     *
     * 何もしません。
     *
     * @param string $name キー
     * @param mixed  $args 値
     *
     * @return bool
     */
    public function __call($name, $args)
    {
        if ($this->_config['debug']) {
            $log = array('name' => $name, 'args' => $args);
            $this->_log->log('BEAR_Cache_Adapter_None', $log);
        }
        return null;
    }
}