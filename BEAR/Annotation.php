<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Annotation
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Annotation.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net/
 */

/**
 * アノテーション
 *
 * @category  BEAR
 * @package   BEAR_Resource
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Annotation.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link      http://www.bear-project.net/
 *
 * @Singleton
 *
 * @config string class  クラス
 * @config string method メソッド
 */
class BEAR_Annotation extends BEAR_Base
{
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['ref']['method'] = new ReflectionMethod($config['class'], $config['method']);
        $this->_config['ref']['class'] = new ReflectionClass($config['class']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->_config['doc']['class'] = $this->_config['ref']['class']->getDocComment();
        /** @noinspection PhpUndefinedMethodInspection */
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
     * @throws BEAR_Annotation_Exception
     */
    public function required(array $values)
    {
        $match = array();
        $result = preg_match_all("/@required\s+(\w+)/is", $this->_config['doc']['method'], $match);
        if ($result !== false) {
            // メソッド優先
            try {
                BEAR_Util::required($match[1], $values);
            } catch (Exception $e) {
                $info = array(
                    'RO' => $this->_config['ref']['method']->class . '::' . $this->_config['ref']['method']->name,
                    'required' => $match[1],
                    'values' => $values,
                    'doc' => $this->_config['doc']['method']
                );
                $required = implode($match[1], ',');
                $msg = "@required item[{$required}] is missing.";
                throw $this->_exception($msg, array('code' => BEAR::CODE_BAD_REQUEST, 'info' => $info));
            }
        }
    }

    /**
     * アスペクトアノテーション
     *
     * @return BEAR_Aspect_Weaver
     */
    public function aspect()
    {
        $match = array();
        preg_match_all("/@aspect\s+(\w+)\s+(\w+)/is", $this->_config['doc']['method'], $match);
        $aspects = array();
        // <アドバイスタイプ> => <アドバイスクラス>の連想配列
        $max = count($match[0]);
        for ($i = 0; $i < $max; $i++) {
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
        $weaver = BEAR::factory('BEAR_Aspect_Weaver', $this->_config);

        return $weaver;
    }
}
