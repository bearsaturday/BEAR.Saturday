<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Smarty factory
 *
 * @Singleton
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
        $smarty = new SmartyBC();
        // フォルダパス設定
        $smarty->setTemplateDir(_BEAR_APP_HOME . $this->_config['path']);
        $smarty->setConfigDir(_BEAR_APP_HOME . '/App/smarty/configs/');
        $smarty->setCompileDir(isset($this->_config['compile_dir']) ? $this->_config['compile_dir'] : _BEAR_APP_HOME . '/tmp/smarty_templates_c/');
        $smarty->setCompileId(isset($this->_config['ua']) ? $this->_config['ua'] : '');
        $smarty->setCacheDir(isset($this->_config['cache_dir']) ? $this->_config['cache_dir'] : _BEAR_APP_HOME . '/tmp/smarty_cache/');
        $smarty->addPluginsDir([
            _BEAR_APP_HOME . '/App/smarty/plugins/',
            _BEAR_BEAR_HOME . '/BEAR/Smarty/plugins/'
        ]);
        $smarty->setCaching($this->_config['caching']);
        $smarty->setCacheLifetime($this->_config['cache_lifetime']);
        $smarty->setCompileCheck(false);
        // デバックモード
        if ($this->_config['debug']) {
            // テンプレートキャッシュは常に再生成
            $smarty->setForceCompile(true);
        }

        return $smarty;
    }
}
