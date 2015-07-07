<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Smarty
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Smarty.php 2538 2011-06-12 17:37:53Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net/
 */

/**
 * Smartyライブラリ読み込み
 */
require _BEAR_BEAR_HOME . '/BEAR/vendors/Smarty/libs/Smarty.class.php';

/**
 * Smarty factory
 *
 * @category  BEAR
 * @package   BEAR_Smarty
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Smarty.php 2538 2011-06-12 17:37:53Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net/
 *
 * @instance singleton
 *
 * @config string ua UAコード
 * @config string compile_dir
 * @config string cache_dir
 */
class BEAR_Smarty extends BEAR_Factory
{
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $app = BEAR::get('app');
        $this->_config['path'] = $app['BEAR_View']['path'];
    }

    /**
     * Return smarty object
     *
     * @return Smarty
     */
    public function factory()
    {
        $smarty = new Smarty();
        // フォルダパス設定
        $smarty->template_dir = _BEAR_APP_HOME . $this->_config['path'];
        $smarty->config_dir = _BEAR_APP_HOME . '/App/smarty/configs/';
        $smarty->compile_dir = isset($this->_config['compile_dir']) ? $this->_config['compile_dir'] : _BEAR_APP_HOME . '/tmp/smarty_templates_c/';
        $smarty->compile_id = isset($this->_config['ua']) ? $this->_config['ua'] : '';
        $smarty->cache_dir = isset($this->_config['cache_dir']) ? $this->_config['cache_dir'] : _BEAR_APP_HOME . '/tmp/smarty_cache/';
        $smarty->plugins_dir = array(
            'plugins',
            'App/smarty/plugins/',
            'BEAR/Smarty/plugins/'
        );
        $smarty->caching = $this->_config['caching'];
        $smarty->cache_lifetime = $this->_config['cache_lifetime'];
        $smarty->compile_check = false;
        // デバックモード
        if ($this->_config['debug']) {
            // テンプレートキャッシュは常に再生成
            $smarty->force_compile = true;
        }

        return $smarty;
    }
}
