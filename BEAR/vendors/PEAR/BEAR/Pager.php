<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Pager
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Pager.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Pager/BEAR_Pager.html
 */
/**
 * ページャークラス
 *
 * <pre>
 *  データを分割して表示するページャークラスです。
 *　BEAR_Resourceクラスと組み合わせて、viewのスライス
 * やリンクの生成などを行います。
 *
 * このクラスではDBページングの機能は持っていません。
 * ページャーの扱えるDBから取得するなどデータを全て用意
 * してからデータスライスします。
 *
 * DBページングはBEAR_DBクラスで実装されています。
 *
 * Example 1. 10アイテムでページング
 * </pre>
 *
 * <code>
 * $pager = new BEAR_Pager();
 * $pager->setOption('perPage', 10);                  // 10アイテム/page
 * $pager->setOption('linkClass', $linkCssClass);    // HTML CSS指定
 * $pager->makePage($itemData);
 * $view['data'] = $pager->getResult();
 * $view['pager'] = $pager->getLinks();
 * </code>
 * <pre>
 *
 * Example 2. Pagerオプション設定
 * </pre>
 *
 * <code>
 * //ページャー設定
 * BEAR_Pager::$optionsPc = array('separator'=>'|');
 * BEAR_Pager::$optionsMobile = array('separator'=>'');
 * </code>
 *
 * @category  BEAR
 * @package   BEAR_Pager
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Pager.php 1201 2009-11-10 06:39:01Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Pager/BEAR_Pager.html
 *  */
class BEAR_Pager extends BEAR_Base
{

    /**
     * ページャーキー
     *
     */
    const PAGER_NUM = '_start';

    /*
     * ページャーCSSクラス
     */
    const PAGER_CLASS = 'pager';

    /**
     * PEARページャークラス
     *
     * @var object
     */
    public $pager;

    /**
     * ページャーオプション
     *
     * @var     array
     * @access  private
     * @see http://pear.php.net/manual/ja/package.html.pager.factory.php
     */
    private $_options;

    /**
     * ページャーでスライスされたビュー
     *
     * @var array
     * @access  private
     */
    private $_pagerResult;

    /**
     * ページャーリンクHTML
     *
     * @var string
     * @access private
     */
    private $_links;

    /**
     * PC用ページャーオプション
     */
    public static $optionsPc = array();

    /**
     * モバイル用ページャーオプション
     */
    public static $optionsMobile = array();

    /**
     * コンストラクタ
     *
     * エージェントに応じて（PC,携帯）ページャーオプションを変えます。
     *
     * @see http://pear.php.net/manual/ja/package.html.pager.factory.php
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function onInject()
    {
        $this->_ua = BEAR::dependency('BEAR_Agent')->getUa();
        $sessionId = session_name();
        switch ($this->_ua) {
        // PC
        case BEAR_Agent::UA_DEFAULT :
            $this->_options = array('perPage' => 10,  // ページごとに表示するアイテムの数
                'delta' => 10,  // 現在のページの前後に表示するページ番号の数
                'urlVar' => self::PAGER_NUM,  // ページ番号を示すためのURL変数名
                'prevImg' => '前へ',  // Prevボタン（IMGタグをつけてグラフィック表示も可能）
                'nextImg' => '次へ',  // Nextボタン（IMGタグをつけてグラフィック表示も可能）
                'separator' => ' ',  // セパレーター
                'linkClass' => self::PAGER_CLASS,  // リンクスタイルのためのCSSクラス名
                'totalItems' => 100,  // アイテム総数
                'excludeVars' => array(
                    $sessionId));
            break;
        // Mobile
        default :
            $this->_options = array('perPage' => 10,
                'delta' => 5,
                'altNext' => 'Next',
                'urlVar' => self::PAGER_NUM,
                'prevImg' => '<<(*)',
                'nextImg' => '>>(#)',
                'separator' => ' ',
                'totalItems' => 100,
                'excludeVars' => array($sessionId));
            break;
        }
        // オプションを指定されていれば値があればオーバーライド
        if ($this->_ua == BEAR_Agent::UA_DEFAULT && self::$optionsPc) {
            $this->_options = array_merge($this->_options, self::$optionsPc);
        } elseif ($this->_ua != BEAR_Agent::UA_DEFAULT && self::$optionsMobile) {
            $this->_options = array_merge($this->_options, self::$optionsMobile);
        }
        // ページャーオブジェクト
        $this->pager = Pager::factory();
    }

    /**
     * ページャーオプションの取得
     *
     * @return array
     */
    public function getPagerOptions()
    {
        return $this->_options;
    }

    /**
     * ページング
     *
     * <pre>
     * ページング処理します。
     * ページングされた結果とページナビゲーションHTMLの生成を
     * プロパティに保持します。
     * </pre>
     *
     * @param array $view ページングするデータアイテム
     *
     * @return void
     */
    function makePager($view)
    {
        if (is_null($view)) {
            //$viewが無い
            $this->_links = null;
            return;
        } else {
            $this->_options['itemData'] = $view;
            // Pager オブジェクトを作成
            $this->pager->setOptions($this->_options);
            $this->pager->build();
            // ページデータを取得
            $this->_pagerResult = $this->pager->getPageData();
            // リンク
            $this->_links = $this->pager->getLinks();
        }
    }

    /**
     * ページングリンク生成
     *
     * データをページングしないでリンクのみ生成します
     *
     * @param string $delta      デルタオプション
     * @param string $totalItems トータルアイテム数
     *
     * @return array リンク
     */
    function makeLinks($delta, $totalItems)
    {
        $this->_options['delta'] = $delta;
        $this->_options['totalItems'] = $totalItems;
        // Pager オブジェクトを作成
        $this->pager->setOptions($this->_options);
        $this->pager->build();
        $this->_links = $this->pager->getLinks();
        return $this->_links;
    }

    /**
     * ページャーオプションの設定
     *
     * オプションを１つ設定します。
     *
     * @param string $key    オプションキ-
     * @param string $option オプション値
     *
     * @return   void
     * @see     http://pear.php.net/manual/ja/package.html.pager.factory.php
     * @see BEAR_Pager::setOptions(9
     */
    public function setOption($key, $option)
    {
        $this->_options[$key] = $option;
    }

    /**
     * ページャーオプション設定
     *
     * ページャーのオプションを連想配列で指定します。
     *
     * @param array $options オプション
     *
     * @return void
     *
     * @see http://pear.php.net/manual/ja/package.html.pager.factory.php
     * @see BEAR_Pager::setOption()
     *      */
    function setOptions(array $options)
    {
        $this->_options = $options + $this->_options;
    }

    /**
     * ページング結果の取得
     *
     * ページングされた結果の取得を行います。
     *
     * @return   array  ページングされた結果
     */
    public function getResult()
    {
        return $this->_pagerResult;
    }

    /**
     * ナビゲートリンクの取得
     *
     * <pre>
     * ページのナビゲートHTMLを取得します。
     * エージェントに応じたHTMLを生成し、携帯の場合はアクセスキーが利用できます。
     * </pre>
     *
     * @param mixed $links false | リンクHTML配列
     *
     * @return array ナビゲートリンクHTML
     */
    public function getLinks($links = false)
    {
        assert(is_object($this->pager));
        $links = ($links) ? $links : $this->_links;
        // PC
        if ($this->_ua == BEAR_Agent::UA_DEFAULT) {
            return $links;
        }
        // 携帯 $links['all']を書き換え
        $hasBack = $hasNext = false;
        if ($links['next']) {
            $next = str_replace(' title=', ' accesskey="#" title=', $links['next']);
            $hasNext = true;
        }
        if ($links['back']) {
            $back = str_replace(' title=', ' accesskey="*" title=', $links['back']);
            $hasBack = true;
        }
        $links['back_mobile'] = $back;
        $links['next_mobile'] = $next;
        /** @var $this->pager Pager */
        $current = $this->pager->getCurrentPageID();
        $total = $this->pager->numPages();
        switch (array($hasBack, $hasNext)) {
        case array(false, true) :
            $links['all'] = "<font color=gray >{$this->_options['prevImg']} $current/$total</font> {$next}";
            break;
        case array(true, false) :
            $links['all'] = "{$back} <font color=gray>$current/$total {$this->_options['nextImg']}</font>";
            break;
        case array(true, true) :
            $links['all'] = "{$back} | {$next}";
            $links['all'] = "{$back} <font color=gray>$current/$total</font> {$next}";
        default :
            break;
        }
        // セッションクエリーを削除
        return $links;
    }

    /**
     * AJAX用ページャーリンクを取得
     *
     * リンクにrel="bear"をつけbear.js用のAJAXのリンクにします
     *
     * @return array
     * @ignore
     */
    public function getAjaxLinks()
    {
        foreach ($key as $value) {
            $value = preg_replace('/<a\s/', '<a rel="bear" >', $value);
            $array[$key] = $value;
        }
        return $array;
    }
}