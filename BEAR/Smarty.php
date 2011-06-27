<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Smarty
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Smarty.php 2538 2011-06-12 17:37:53Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * Smartyライブラリ読み込み
 */
require _BEAR_BEAR_HOME . '/BEAR/vendors/Smarty/libs/Smarty.class.php';

/**
 * Smarty
 *
 * <pre>
 * BEARで使うテンプレートエンジンのSmartyです。コンストラクタで
 * 初期設定をしています。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Smarty
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Smarty.php 2538 2011-06-12 17:37:53Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 *
 * @instance singleton
 *
 * @config string ua UAコード
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
     * Smartyオブジェクトを生成
     *
     * @return Smarty
     */
    public function factory()
    {
        //親コンストラクタ
        $smarty = new Smarty();
        //フォルダパス設定
        $smarty->template_dir = _BEAR_APP_HOME . $this->_config['path'];
        $smarty->config_dir = _BEAR_APP_HOME . '/App/smarty/configs/';
        $smarty->compile_dir = _BEAR_APP_HOME . '/tmp/smarty_templates_c/';
        $smarty->compile_id = isset($this->_config['ua']) ? $this->_config['ua'] : '';
        $smarty->cache_dir = _BEAR_APP_HOME . '/tmp/smarty_cache/';
        $smarty->plugins_dir = array('plugins',
            'App/smarty/plugins/',
            'BEAR/Smarty/plugins/');
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