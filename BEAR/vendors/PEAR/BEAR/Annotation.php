<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Annotation
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Annotation.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * アノテーションクラス
 *
 * <pre>
 * phpDocのコメントから特定のパラメーターをアノテーションとして取り扱います。
 *
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Annotation.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 * @abstract
 */
class BEAR_Annotation extends BEAR_Base
{

    /**
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['ref']['method'] = new ReflectionMethod($config['class'], $config['method']);
        $this->_config['ref']['class'] = new ReflectionClass($config['class']);
        $this->_config['doc']['class'] = $this->_config['ref']['class']->getDocComment();
        $this->_config['doc']['method'] = $this->_config['ref']['method']->getDocComment();
    }

    /**
     * メソッドの必須項目をチェック
     *
     * 連想配列で受け取った値に必要なキーがあるかチェックします。
     * 問題ががればBEAR_Exceptionが投げられます。
     *
     * @param array $values 配列
     *
     * @return void
     */
    public function required($values)
    {
        $match = array();
        $result = preg_match_all("/@required\s+(\w+)/is", $this->_config['doc']['method'], $match);
        if ($result !== false) {
            //メソッド優先
            try {
                BEAR_Util::required($match[1], $values);
            } catch(Exception $e) {
                $info = array('RO'=>$this->_config['ref']['method']->class . '::' . $this->_config['ref']['method']->name,
                 'required' => $match[1], 'values'=>$values, 'doc'=>$this->_config['doc']['method']);
                $msg = "Bad Request @required Annotation Exception";
                throw $this->_exception($msg, array(
                    'code' => BEAR::CODE_BAD_REQUEST,
                    'info' => $info));
            }
        }
    }

    /**
     * アスペクトアノテーション
     *
     * @return void
     *
     */
    public function aspect()
    {
        $match = array();
        $result = preg_match_all("/@aspect\s+(\w+)\s+(\w+)/is", $this->_config['doc']['method'], $match);
        $aspects = array();
        // <アドバイスタイプ> => <アドバイスクラス>の連想配列
        for ($i = 0; $i < count($match[0]); $i++) {
            $type = $match[1][$i];
            $class = $match[2][$i];
            $aspects[$type][] = $class;
        }
        if (!$aspects) {
            $method = new ReflectionMethod($this->_config['class'], $this->_config['method']);
            return $method;
        }
        $this->_config['aspects'] = $aspects;
        // weaver
        $weaver = BEAR::dependency('BEAR_Aspect_Weaver', $this->_config);
        return $weaver;
    }
}