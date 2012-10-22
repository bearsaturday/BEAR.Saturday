<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Smarty
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Smarty.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Smarty/BEAR_Smarty.html
 */
/**
 * Smartyライブラリ読み込み
 */
include _BEAR_BEAR_HOME . '/BEAR/inc/Smarty/libs/Smarty.class.php';
/**
 * Smartyクラス
 *
 * <pre>
 * BEARで使うテンプレートエンジンのSmartyです。コンストラクタで
 * 初期設定をしています。
 *
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Smarty
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Smarty.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Smarty/BEAR_Smarty.html
 */
class BEAR_Smarty extends BEAR_Factory
{
    /**
     * コンストラクタ
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Smartyオブジェクトを生成
     */
    public function factory()
    {
        //親コンストラクタ
        $smarty = new Smarty();
        //フォルダパス設定
        $smarty->template_dir = _BEAR_APP_HOME . '/App/views/';
        $smarty->config_dir = _BEAR_APP_HOME . '/App/smarty/configs/';
        $smarty->compile_dir = _BEAR_APP_HOME . '/tmp/smarty_templates_c/';
        $smarty->compile_id = $this->_config['ua'];
        $smarty->cache_dir = _BEAR_APP_HOME . '/tmp/smarty_cache/';
        $smarty->plugins_dir = array('plugins',
            'App/smarty/plugins/',
            'BEAR/Smarty/plugins/');
        // デバックモード
        if ($this->_config['debug']) {
            // テンプレートキャッシュなし
            $smarty->caching = 0;
            // テンプレートキャッシュは常に再生成
            $smarty->force_compile = true;
        }
        return $smarty;
    }
}