<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Page.php 867 2009-09-08 14:43:27Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * Pageリソースクラス
 *
 * <pre>
 * ページをリソースとして扱うクラスです。
 *
 * readがページクラスのonInit()を呼び出しonInit内でsetされたものが結果になって帰ります。フォーマットはROです。
 * create, update, deleteはonAction()を呼び出します。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Page.php 867 2009-09-08 14:43:27Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 *  */
class BEAR_Resource_Execute_Page extends BEAR_Resource_Execute_Adaptor
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
     * リソースアクセス
     *
     * リソースを使用します。
     *
     * @param void
     *
     * @return mixed
     */
    public function request()
    {
        $pageRawPath = substr($this->_config['uri'], 7);
        $pageClass = 'page_' . str_replace('/', '_', $pageRawPath);
        if (!class_exists($pageClass, false)) {
            $pageFile = str_replace('/', DIRECTORY_SEPARATOR, $pageRawPath) . '.php';
            BEAR_Main::includePage($pageFile);
        }
        $pageConfig = array('resource_id'=>$pageClass, 'mode'=>BEAR_Page::CONFIG_MODE_RESOURCE);
        $pageOptions = $this->_config['options'];
        $page = BEAR::factory($pageClass, $pageConfig, $pageOptions);
        BEAR_Ro::setPage($page);
        $method = ($this->_config['method'] === 'read') ? 'onInit' : 'onAction';
        $args = array_merge($page->getArgs(), $this->_config['values']);
        $page->$method($args);
        $pageValues = $page->getValues();
        $roValues = BEAR_Ro::getValues();
        $result = array_merge($pageValues, $roValues);
        BEAR_Ro::setPage();
        return $result;
        //        // アノテーションクラスDI
        //        $config['method'] = 'on' . $this->_config['method'];
        //        $annotation = BEAR::factory('BEAR_Annotation', $config);
        //        //        // requireアノテーション (引数のチェック)
        //        $annotation->required($this->_config['values']);
        //        //        // aspectアノテーション (アドバイスの織り込み）
        //        $method = $annotation->aspect();
        //        $result = $method->invoke($this->_config['obj'], $this->_config['values']);
        //        // 後処理
        //        if (PEAR::isError($result)) {
        //            $this->_config['obj']->setCode(BEAR::CODE_ERROR);
        //        } else {
        //            if ($result instanceof BEAR_Ro) {
        //                // return RO
        //                return $result;
        //            } else {
        //                $this->_config['obj']->setBody($result);
        //            }
        //        }
        //        return $this->_config['obj'];
    }
}
